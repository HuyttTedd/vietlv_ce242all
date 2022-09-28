<?php

namespace Amasty\PushNotifications\Model;

use Amasty\PushNotifications\Api\CampaignRepositoryInterface;
use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Amasty\PushNotifications\Model\OptionSource\Campaign\Active;
use Amasty\PushNotifications\Model\OptionSource\Campaign\NotificationType;
use Amasty\PushNotifications\Model\OptionSource\Campaign\Status;
use Amasty\PushNotifications\Model\ResourceModel\Campaign as CampaignResource;
use Amasty\PushNotifications\Model\ResourceModel\Campaign\Collection;
use Amasty\PushNotifications\Model\ResourceModel\Campaign\CollectionFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;

class CampaignRepository implements CampaignRepositoryInterface
{
    /**
     * This field adds to the campaign with true value, when status change is not required before model save
     */
    const SKIP_STATUS_CHANGE = 'skip_status_change';

    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CampaignFactory
     */
    private $campaignFactory;

    /**
     * @var CampaignResource
     */
    private $campaignResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $campaign;

    /**
     * @var CollectionFactory
     */
    private $campaignCollectionFactory;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        CampaignFactory $campaignFactory,
        CampaignResource $campaignResource,
        CollectionFactory $campaignCollectionFactory
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->campaignFactory = $campaignFactory;
        $this->campaignResource = $campaignResource;
        $this->campaignCollectionFactory = $campaignCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(CampaignInterface $campaign)
    {
        try {
            $campaign = $this->prepareCampaignForSave($campaign);

            $this->campaignResource->save($campaign);
            unset($this->campaign[$campaign->getCampaignId()]);
        } catch (\Exception $e) {
            if ($campaign->getCampaignId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save campaign with ID %1. Error: %2',
                        [$campaign->getCampaignId(), $e->getMessage()]
                    )
                );
            }

            throw new CouldNotSaveException(__('Unable to save new campaign. Error: %1', $e->getMessage()));
        }

        return $campaign;
    }

    /**
     * @inheritdoc
     */
    public function getById($campaignId)
    {
        if (!isset($this->campaign[$campaignId])) {
            /** @var \Amasty\PushNotifications\Model\Campaign $campaign */
            $campaign = $this->campaignFactory->create();
            $this->campaignResource->load($campaign, $campaignId);

            if (!$campaign->getCampaignId()) {
                throw new NoSuchEntityException(__('Campaign with specified ID "%1" not found.', $campaignId));
            }

            $this->campaign[$campaignId] = $campaign;
        }

        return $this->campaign[$campaignId];
    }

    /**
     * @param int $campaignId
     *
     * @return $this
     *
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function increaseClickCounter($campaignId)
    {
        $campaign = $this->getById($campaignId);
        $campaign->setClickedCounter((int)$campaign->getClickedCounter() + 1);
        $campaign->setData('skip_status_change', true);

        $this->save($campaign);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function delete(CampaignInterface $campaign)
    {
        try {
            $this->campaignResource->delete($campaign);
            unset($this->campaign[$campaign->getCampaignId()]);
        } catch (\Exception $e) {
            if ($campaign->getCampaignId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove question with ID %1. Error: %2',
                        [$campaign->getCampaignId(), $e->getMessage()]
                    )
                );
            }

            throw new CouldNotDeleteException(__('Unable to remove campaign. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($campaignId)
    {
        $campaignModel = $this->getById($campaignId);
        $this->delete($campaignModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        /** @var \Amasty\PushNotifications\Model\ResourceModel\Campaign\Collection $campaignCollection */
        $campaignCollection = $this->campaignCollectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $campaignCollection);
        }

        $searchResults->setTotalCount($campaignCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $campaignCollection);
        }

        $campaignCollection->setCurPage($searchCriteria->getCurrentPage());
        $campaignCollection->setPageSize($searchCriteria->getPageSize());
        $campaign = [];

        /** @var CampaignInterface $campaign */
        foreach ($campaignCollection->getItems() as $campaign) {
            $campaign[] = $this->getById($campaign->getId());
        }

        $searchResults->setItems($campaign);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $campaignCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $campaignCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $campaignCollection->addFieldToFilter(
                $filter->getField(),
                [
                    $condition => $filter->getValue()
                ]
            );
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection  $campaignCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $campaignCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $campaignCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? 'DESC' : 'ASC'
            );
        }
    }

    /**
     * @param CampaignInterface $campaign
     *
     * @return CampaignInterface
     *
     * @throws NoSuchEntityException
     */
    private function prepareCampaignForSave(CampaignInterface $campaign)
    {
        if ($campaign->getCampaignId()) {
            $savedCampaign = $this->getById($campaign->getCampaignId());
            $this->setCorrectStatus($campaign);
            $savedCampaign->addData($campaign->getData());

            return $savedCampaign;
        } else {
            $status = $campaign->getNotificationType() === NotificationType::EVENT_TYPE
                ? Status::STATUS_BY_EVENT
                : Status::STATUS_SCHEDULED;
            $campaign->setStatus($status);
        }

        return $campaign;
    }

    /**
     * @param CampaignInterface $campaign
     */
    private function setCorrectStatus($campaign)
    {
        if ($campaign->getStatus() != Status::STATUS_SCHEDULED
            && !$campaign->getData(self::SKIP_STATUS_CHANGE)
        ) {
            $status = $campaign->getIsActive() == Active::STATUS_INACTIVE
                    ? Status::STATUS_EDITED
                    : Status::STATUS_SCHEDULED;
            $campaign->setStatus($status);
        }
        if ($campaign->getNotificationType() === NotificationType::EVENT_TYPE) {
            $campaign->setStatus(Status::STATUS_BY_EVENT);
        }
    }
}
