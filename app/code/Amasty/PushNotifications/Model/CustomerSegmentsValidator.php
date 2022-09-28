<?php

namespace Amasty\PushNotifications\Model;

use Amasty\PushNotifications\Api\Data\SubscriberInterface;
use Amasty\PushNotifications\Exception\NotificationException;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class will be used as proxy in CampaignProcessor
 * due to operating with Amasty Customer Segments module
 * which can be not installed
 */
class CustomerSegmentsValidator
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param array $subscriberInfo
     * @param array $segments
     *
     * @throws NotificationException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function validateSegments(&$subscriberInfo, $segments)
    {
        if (empty($segments)) {
            return;
        }
        foreach ($subscriberInfo as $key => $item) {
            if ($customerId = (int)$item[SubscriberInterface::CUSTOMER_ID]) {
                if (empty($this->objectManager->create(\Amasty\Segments\Model\ResourceModel\Index::class)
                    ->checkValidCustomerFromIndex($segments, $customerId, 'customer_id'))
                ) {
                    unset($subscriberInfo[$key]);
                }
            } else {
                unset($subscriberInfo[$key]);
            }
        }
    }
}
