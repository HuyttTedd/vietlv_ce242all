<?php

namespace Amasty\PushNotifications\Api;

/**
 * @api
 */
interface SubscriberRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\PushNotifications\Api\Data\SubscriberInterface $subscriber
     * @return \Amasty\PushNotifications\Api\Data\SubscriberInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Amasty\PushNotifications\Api\Data\SubscriberInterface $subscriber);

    /**
     * Get by id
     *
     * @param int $subscriberId
     * @return \Amasty\PushNotifications\Api\Data\SubscriberInterface
     * @throws \Amasty\PushNotifications\Exception\NotificationException
     */
    public function getById($subscriberId);

    /**
     * Get by token
     *
     * @param string $token
     * @return \Amasty\PushNotifications\Api\Data\SubscriberInterface
     * @throws \Amasty\PushNotifications\Exception\NotificationException
     */
    public function getByToken($token);

    /**
     * Get by Customer Id or Visitor Id
     *
     * @param string $customerId
     * @param string $visitorId
     * @return \Amasty\PushNotifications\Api\Data\SubscriberInterface
     * @throws \Amasty\PushNotifications\Exception\NotificationException
     */
    public function getByCustomerVisitor($customerId, $visitorId);

    /**
     * Delete
     *
     * @param \Amasty\PushNotifications\Api\Data\SubscriberInterface $subscriber
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\PushNotifications\Api\Data\SubscriberInterface $subscriber);

    /**
     * Delete by id
     *
     * @param int $subscriberId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($subscriberId);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
