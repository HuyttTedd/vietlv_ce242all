<?php

namespace Amasty\PushNotifications\Ui\DataProvider\Listing;

use Amasty\PushNotifications\Api\Data\SubscriberInterface;
use Amasty\PushNotifications\Model\ResourceModel\Subscriber\Collection;
use Amasty\PushNotifications\Api\SubscriberRepositoryInterface;

class SubscriberDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var SubscriberRepositoryInterface
     */
    private $repository;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Collection $collection,
        SubscriberRepositoryInterface $repository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collection;
        $this->repository = $repository;
    }

    /**
     * Get data
     *
     * @return array
     *
     * @throws \Amasty\PushNotifications\Exception\NotificationException
     */
    public function getData()
    {
        $data = parent::getData();
        foreach ($data['items'] as $key => $subscriber) {
            $subscriberData = $this->repository->getById($subscriber[SubscriberInterface::SUBSCRIBER_ID])->getData();
            if (!$subscriberData[SubscriberInterface::CUSTOMER_ID]) {
                $subscriberData[SubscriberInterface::CUSTOMER_ID] = __('Guest');
            }
            $data['items'][$key] = $subscriberData;
        }

        return $data;
    }
}
