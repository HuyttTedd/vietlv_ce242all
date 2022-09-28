<?php

namespace Amasty\PushNotifications\Model;

use Amasty\PushNotifications\Api\Data\SubscriberInterface;
use Amasty\PushNotifications\Api\SubscriberRepositoryInterface;
use Amasty\PushNotifications\Exception\NotificationException;
use Amasty\PushNotifications\Model\SubscriberFactory;
use Amasty\PushNotifications\Model\ResourceModel\Subscriber as SubscriberResource;
use Amasty\PushNotifications\Model\ResourceModel\Subscriber\CollectionFactory;
use Amasty\PushNotifications\Model\ResourceModel\Subscriber\Collection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;

class SubscriberRepository implements SubscriberRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var SubscriberResource
     */
    private $subscriberResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $subscriber;

    /**
     * @var CollectionFactory
     */
    private $subscriberCollectionFactory;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        SubscriberFactory $subscriberFactory,
        SubscriberResource $subscriberResource,
        CollectionFactory $subscriberCollectionFactory
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->subscriberFactory = $subscriberFactory;
        $this->subscriberResource = $subscriberResource;
        $this->subscriberCollectionFactory = $subscriberCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(SubscriberInterface $subscriber)
    {
        try {
            $subscriber = $this->prepareSubscriberForSave($subscriber);

            $this->subscriberResource->save($subscriber);
            unset($this->subscriber[$subscriber->getSubscriberId()]);
        } catch (\Exception $e) {
            if ($subscriber->getSubscriberId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save subscriber with ID %1. Error: %2',
                        [$subscriber->getSubscriberId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new subscriber. Error: %1', $e->getMessage()));
        }

        return $subscriber;
    }

    /**
     * @inheritdoc
     */
    public function getById($subscriberId)
    {
        if (!isset($this->subscriber[$subscriberId])) {
            return $this->getSubscriberByField($subscriberId);
        }

        return $this->subscriber[$subscriberId];
    }

    /**
     * @inheritdoc
     */
    public function getByToken($token)
    {
        return $this->getSubscriberByField($token, SubscriberInterface::TOKEN);
    }

    /**
     * @inheritdoc
     */
    public function getByCustomerVisitor($customerId, $visitorId)
    {
        if ($customerId) {
            return $this->getSubscriberByField($customerId, SubscriberInterface::CUSTOMER_ID);
        } elseif ($visitorId) {
            return $this->getSubscriberByField($visitorId, SubscriberInterface::VISITOR_ID);
        }

        throw new NotificationException(__('Customer Id or Visitor Id is not defined'));
    }

    /**
     * @param string|int $fieldValue
     * @param string $fieldName
     *
     * @return Subscriber|bool
     *
     * @throws NotificationException
     */
    private function getSubscriberByField(
        $fieldValue,
        $fieldName = SubscriberInterface::SUBSCRIBER_ID
    ) {
        if ($fieldValue) {

            /** @var \Amasty\PushNotifications\Model\Subscriber $subscriber */
            $subscriber = $this->subscriberFactory->create();
            $this->subscriberResource->load($subscriber, $fieldValue, $fieldName);

            if (!$subscriber->getSubscriberId()) {
                return false;
            }

            $this->subscriber[$subscriber->getSubscriberId()] = $subscriber;

            return $subscriber;
        }

        throw new NotificationException(__('Field value is not defined.'));
    }

    /**
     * @inheritdoc
     */
    public function delete(SubscriberInterface $subscriber)
    {
        try {
            $this->subscriberResource->delete($subscriber);
            unset($this->subscriber[$subscriber->getSubscriberId()]);
        } catch (\Exception $e) {
            if ($subscriber->getSubscriberId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove question with ID %1. Error: %2',
                        [$subscriber->getSubscriberId(), $e->getMessage()]
                    )
                );
            }

            throw new CouldNotDeleteException(__('Unable to remove subscriber. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($subscriberId)
    {
        $subscriberModel = $this->getById($subscriberId);
        $this->delete($subscriberModel);

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
        $subscriberCollection = $this->subscriberCollectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $subscriberCollection);
        }

        $searchResults->setTotalCount($subscriberCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $subscriberCollection);
        }

        $subscriberCollection->setCurPage($searchCriteria->getCurrentPage());
        $subscriberCollection->setPageSize($searchCriteria->getPageSize());
        $subscriber = [];

        /** @var SubscriberInterface $subscriber */
        foreach ($subscriberCollection->getItems() as $subscriber) {
            $subscriber[] = $this->getById($subscriber->getSubscriberId());
        }

        $searchResults->setItems($subscriber);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $subscriberCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $subscriberCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $subscriberCollection->addFieldToFilter(
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
     * @param Collection  $subscriberCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $subscriberCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $subscriberCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? 'DESC' : 'ASC'
            );
        }
    }

    /**
     * @param SubscriberInterface $subscriber
     *
     * @return SubscriberInterface|mixed
     *
     * @throws NoSuchEntityException
     */
    private function prepareSubscriberForSave(SubscriberInterface $subscriber)
    {
        if ($subscriber->getSubscriberId()) {
            $savedSubscriber = $this->getById($subscriber->getSubscriberId());
            $savedSubscriber->addData($subscriber->getData());

            return $savedSubscriber;
        }

        return $subscriber;
    }
}
