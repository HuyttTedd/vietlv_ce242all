<?php

namespace Amasty\PushNotifications\Model\Processor;

use Amasty\Base\Ui\Component\Listing\Column\StoreOptions;
use Amasty\PushNotifications\Api\CampaignRepositoryInterface;
use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Amasty\PushNotifications\Api\Data\SubscriberInterface;
use Amasty\PushNotifications\Exception\NotificationException;
use Amasty\PushNotifications\Model\Builder\DateTimeBuilder;
use Amasty\PushNotifications\Model\CustomerSegmentsValidator;
use Amasty\PushNotifications\Model\OptionSource\Campaign\SegmentationSource;
use Amasty\PushNotifications\Model\ResourceModel\Campaign\CollectionFactory as CampaignCollectionFactory;
use Amasty\PushNotifications\Model\ResourceModel\Subscriber\CollectionFactory as SubscriberCollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\GroupManagement;

class CampaignProcessor
{
    /**
     * @var \Amasty\PushNotifications\Model\Processor\NotificationProcessor
     */
    private $notificationProcessor;

    /**
     * @var DateTimeBuilder
     */
    private $dateTimeBuilder;

    /**
     * @var CampaignCollectionFactory
     */
    private $campaignCollectionFactory;

    /**
     * @var SubscriberCollectionFactory
     */
    private $subscriberCollectionFactory;

    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerSegmentsValidator
     */
    private $customerSegmentsValidator;

    public function __construct(
        NotificationProcessor $notificationProcessor,
        DateTimeBuilder $dateTimeBuilder,
        CampaignCollectionFactory $campaignCollectionFactory,
        SubscriberCollectionFactory $subscriberCollectionFactory,
        CampaignRepositoryInterface $campaignRepository,
        CustomerRepositoryInterface $customerRepository,
        CustomerSegmentsValidator $customerSegmentsValidator
    ) {
        $this->notificationProcessor = $notificationProcessor;
        $this->dateTimeBuilder = $dateTimeBuilder;
        $this->campaignCollectionFactory = $campaignCollectionFactory;
        $this->subscriberCollectionFactory = $subscriberCollectionFactory;
        $this->campaignRepository = $campaignRepository;
        $this->customerRepository = $customerRepository;
        $this->customerSegmentsValidator = $customerSegmentsValidator;
    }

    /**
     * @inheritdoc
     */
    public function processBySchedule()
    {
        $campaigns = $this->getScheduleCampaigns();
        $subscribersInfoByStore = $this->getValidSubscribers();

        if ($campaigns && $subscribersInfoByStore) {
            /** @var \Amasty\PushNotifications\Model\Campaign $campaign */
            foreach ($campaigns as $campaign) {
                $this->processCampaign($campaign, $subscribersInfoByStore);
            }
        } else {
            throw new NotificationException(__('No valid Campaigns or Subscribers was found.'));
        }
    }

    public function processByEvent(string $event, array $customerIds)
    {
        $campaigns = $this->getEventCampaigns($event);
        $subscribersInfoByStore = $this->getValidSubscribers($customerIds);

        if ($campaigns && $subscribersInfoByStore) {
            /** @var \Amasty\PushNotifications\Model\Campaign $campaign */
            foreach ($campaigns as $campaign) {
                $this->processCampaign($campaign, $subscribersInfoByStore);
            }
        } else {
            throw new NotificationException(__('No valid Campaigns or Subscribers was found.'));
        }
    }

    /**
     * @param array $subscriberInfo
     * @param array $customerGroups
     *
     * @throws NotificationException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function validateCustomerGroups(&$subscriberInfo, $customerGroups)
    {
        if (empty($customerGroups)) {
            return;
        }

        foreach ($subscriberInfo as $key => $item) {
            if ($customerId = (int)$item[SubscriberInterface::CUSTOMER_ID]) {
                $customer = $this->customerRepository->getById($customerId);

                if (!in_array($customer->getGroupId(), $customerGroups)) {
                    unset($subscriberInfo[$key]);
                }
            } elseif (!in_array(GroupManagement::NOT_LOGGED_IN_ID, $customerGroups)) {
                unset($subscriberInfo[$key]);
            }
        }
    }

    /**
     * @param \Amasty\PushNotifications\Api\Data\CampaignInterface $campaign
     * @param array $subscribersInfoByStore
     *
     * @return array
     *
     * @throws NotificationException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function processCampaign($campaign, array $subscribersInfoByStore)
    {
        $stores = $campaign->getStores();
        $segmentationSource = $campaign->getSegmentationSource();
        $counts = ['notificationCount' => 0, 'successNotificationCount' => 0];
        foreach ($subscribersInfoByStore as $subscriberStore => $subscriberInfo) {
            if (array_search(StoreOptions::ALL_STORE_VIEWS, $stores) !== false
                || array_search($subscriberStore, $stores) !== false
            ) {
                $segmentationSource == SegmentationSource::CUSTOMER_GROUPS
                    ? $this->validateCustomerGroups($subscriberInfo, $campaign->getCustomerGroups())
                    : $this->customerSegmentsValidator->validateSegments($subscriberInfo, $campaign->getSegments());

                if (empty($subscriberInfo)) {
                    continue;
                }

                $subscriberTokens = array_column($subscriberInfo, SubscriberInterface::TOKEN);
                $subscriberIds = array_column($subscriberInfo, SubscriberInterface::SUBSCRIBER_ID);
                $result = $this->notificationProcessor->processByMultipleTokens(
                    $campaign->getCampaignId(),
                    $subscriberTokens,
                    $subscriberStore
                );
                $counts['notificationCount'] += $result['notificationCount'];
                $counts['successNotificationCount'] += $result['successNotificationCount'];
                $campaign->addSubscriberViews(array_intersect_key(
                    $subscriberIds,
                    $result['successNotificationTokens']
                ));
            }
        }
        $campaign->setSentCounter($campaign->getSentCounter() + $counts['notificationCount']);
        $campaign->setShownCounter($campaign->getShownCounter() + $counts['successNotificationCount']);
        $campaign->processCampaign();
    }

    /**
     * @return \Amasty\PushNotifications\Model\ResourceModel\Campaign\Collection
     */
    private function getCampaignCollection()
    {
        return $this->campaignCollectionFactory->create();
    }

    /**
     * @return \Amasty\PushNotifications\Model\ResourceModel\Subscriber\Collection
     */
    private function getSubscriberCollection()
    {
        return $this->subscriberCollectionFactory->create();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getScheduleCampaigns(): array
    {
        $campaignCollection = $this->getCampaignCollection();
        $campaignCollection
            ->addTimeFilter($this->dateTimeBuilder->getCurrentFormatedTime())
            ->addFilterByStatus()
            ->addFieldToSelect(CampaignInterface::CAMPAIGN_ID);
        $scheduleCampaigns = [];

        foreach ($campaignCollection->getData() as $campaign) {
            $scheduleCampaigns[] = $this->campaignRepository->getById($campaign[CampaignInterface::CAMPAIGN_ID]);
        }

        return $scheduleCampaigns;
    }

    private function getEventCampaigns(string $event): array
    {
        $campaignCollection = $this->getCampaignCollection();
        $campaignCollection->addFilterByEvent($event)
            ->addFieldToSelect(CampaignInterface::CAMPAIGN_ID);
        $eventCampaigns = [];

        foreach ($campaignCollection->getData() as $campaign) {
            $eventCampaigns[] = $this->campaignRepository->getById($campaign[CampaignInterface::CAMPAIGN_ID]);
        }

        return $eventCampaigns;
    }

    private function getValidSubscribers(array $customerIds = []): array
    {
        $subscriberCollection = $this->getSubscriberCollection();
        if ($customerIds) {
            $subscriberCollection->addFieldToFilter(SubscriberInterface::CUSTOMER_ID, ['in' => $customerIds]);
        }
        $subscriberCollection->addActiveFilter();

        if ($subscriberCollection->getSize()) {
            $subscriberCollection->getSubscriberInfoOrderedByStore();
            $subscribers = $subscriberCollection->getData();
            $data = [];

            foreach ($subscribers as $subscriber) {
                $data[$subscriber[SubscriberInterface::STORE_ID]][] = [
                    SubscriberInterface::TOKEN => $subscriber[SubscriberInterface::TOKEN],
                    SubscriberInterface::SUBSCRIBER_ID => $subscriber[SubscriberInterface::SUBSCRIBER_ID],
                    SubscriberInterface::CUSTOMER_ID => $subscriber[SubscriberInterface::CUSTOMER_ID]
                ];
            }

            return $data;
        }

        return [];
    }
}
