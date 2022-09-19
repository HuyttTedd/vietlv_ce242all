<?php

namespace Amasty\PushNotifications\Model\ResourceModel\Subscriber;

use Amasty\PushNotifications\Api\Data\SubscriberInterface;
use Amasty\PushNotifications\Model\OptionSource\Campaign\Active;

/**
 * @method \Amasty\PushNotifications\Model\Subscriber[] getItems()
 * @method \Amasty\PushNotifications\Model\ResourceModel\Subscriber getResource()
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    const CACHE_TAG = 'amasty_notifications_subscriber';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    public function _construct()
    {
        parent::_construct();
        $this->_init(
            \Amasty\PushNotifications\Model\Subscriber::class,
            \Amasty\PushNotifications\Model\ResourceModel\Subscriber::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG];
    }

    /**
     * @return $this
     */
    public function addActiveFilter()
    {
        $this->addFieldToFilter(
            'main_table.' . SubscriberInterface::IS_ACTIVE,
            [
                'eq' => Active::STATUS_ACTIVE
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function getSubscriberInfoOrderedByStore()
    {
        $this->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns([
                SubscriberInterface::TOKEN,
                SubscriberInterface::SUBSCRIBER_ID,
                SubscriberInterface::CUSTOMER_ID,
                SubscriberInterface::STORE_ID
            ])
            ->order(SubscriberInterface::STORE_ID);

        return $this;
    }
}
