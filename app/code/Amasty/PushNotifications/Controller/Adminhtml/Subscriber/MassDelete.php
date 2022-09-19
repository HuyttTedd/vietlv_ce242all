<?php

namespace Amasty\PushNotifications\Controller\Adminhtml\Subscriber;

use Amasty\PushNotifications\Api\CampaignRepositoryInterface;
use Amasty\PushNotifications\Model\CampaignFactory;
use Amasty\PushNotifications\Model\ResourceModel\Campaign\CollectionFactory;
use Amasty\PushNotifications\Model\ResourceModel\Subscriber\CollectionFactory as SubcriberCollectionFactory;
use Amasty\PushNotifications\Model\SubscriberRepository;
use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

class MassDelete extends \Amasty\PushNotifications\Controller\Adminhtml\AbstractMassAction
{
    /**
     * @var SubcriberCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SubscriberRepository
     */
    private $subscriberRepository;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        CampaignRepositoryInterface $repository,
        CollectionFactory $campaignCollectionFactory,
        CampaignFactory $campaignFactory,
        SubcriberCollectionFactory $collectionFactory,
        SubscriberRepository $subscriberRepository
    ) {
        parent::__construct($context, $filter, $logger, $repository, $campaignCollectionFactory, $campaignFactory);
        $this->collectionFactory = $collectionFactory;
        $this->subscriberRepository = $subscriberRepository;
    }

    /**
     * @return \Amasty\PushNotifications\Model\ResourceModel\Campaign\Collection
     */
    protected function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @param $item
     */
    protected function itemAction($item)
    {
        $this->subscriberRepository->deleteById($item->getSubscriberId());
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t delete item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been deleted.', $collectionSize);
        }

        return __('No records have been deleted.');
    }
}
