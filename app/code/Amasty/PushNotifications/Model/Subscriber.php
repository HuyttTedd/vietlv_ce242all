<?php

namespace Amasty\PushNotifications\Model;

use Amasty\PushNotifications\Api\Data\SubscriberInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Amasty\PushNotifications\Model\ResourceModel\Subscriber\Collection;

class Subscriber extends AbstractModel implements SubscriberInterface, IdentityInterface
{
    /**
     * cache tag
     */
    const CACHE_TAG = 'amasty_notifications_subscriber';

    /**
     * @var string
     */
    protected $_cacheTag = true;

    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\PushNotifications\Model\ResourceModel\Subscriber::class);
        $this->setIdFieldName(SubscriberInterface::SUBSCRIBER_ID);
    }

    /**
     * Get identities for cache
     *
     * @return array
     */
    public function getIdentities()
    {
        return [Collection::CACHE_TAG, self::CACHE_TAG . '_' . $this->getQuestionId()];
    }

    /**
     * Get list of cache tags applied to model object.
     *
     * @return array
     */
    public function getCacheTags()
    {
        $tags = parent::getCacheTags();
        if (!$tags) {
            $tags = [];
        }
        return $tags + $this->getIdentities();
    }

    /**
     * @inheritdoc
     */
    public function getSubscriberId()
    {
        return $this->_getData(SubscriberInterface::SUBSCRIBER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setSubscriberId($subscriberId)
    {
        $this->setData(SubscriberInterface::SUBSCRIBER_ID, $subscriberId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSource()
    {
        return $this->_getData(SubscriberInterface::SOURCE);
    }

    /**
     * @inheritdoc
     */
    public function setSource($source)
    {
        $this->setData(SubscriberInterface::SOURCE, $source);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubscribersIp()
    {
        return $this->_getData(SubscriberInterface::SUBSCRIBER_IP);
    }

    /**
     * @inheritdoc
     */
    public function setSubscribersIp($subscribersIp)
    {
        $this->setData(SubscriberInterface::SUBSCRIBER_IP, $subscribersIp);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsActive()
    {
        return $this->_getData(SubscriberInterface::IS_ACTIVE);
    }

    /**
     * @inheritdoc
     */
    public function setIsActive($active)
    {
        $this->setData(SubscriberInterface::IS_ACTIVE, $active);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getToken()
    {
        return $this->_getData(SubscriberInterface::TOKEN);
    }

    /**
     * @inheritdoc
     */
    public function setToken($token)
    {
        $this->setData(SubscriberInterface::TOKEN, $token);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLocation()
    {
        return $this->_getData(SubscriberInterface::LOCATION);
    }

    /**
     * @inheritdoc
     */
    public function setLocation($location)
    {
        $this->setData(SubscriberInterface::LOCATION, $location);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getVisitorId()
    {
        return $this->_getData(SubscriberInterface::VISITOR_ID);
    }

    /**
     * @inheritdoc
     */
    public function setVisitorId($visitorId)
    {
        $this->setData(SubscriberInterface::VISITOR_ID, $visitorId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return $this->_getData(SubscriberInterface::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId($customerId)
    {
        $this->setData(SubscriberInterface::CUSTOMER_ID, $customerId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->_getData(SubscriberInterface::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(SubscriberInterface::UPDATED_AT, $updatedAt);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->_getData(SubscriberInterface::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(SubscriberInterface::CREATED_AT, $createdAt);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        $this->_getData(SubscriberInterface::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId)
    {
        $this->setData(SubscriberInterface::STORE_ID, $storeId);

        return $this;
    }
}
