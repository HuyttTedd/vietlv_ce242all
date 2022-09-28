<?php

namespace Amasty\PushNotifications\Model;

use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Amasty\PushNotifications\Model\OptionSource\Campaign\Active;
use Amasty\PushNotifications\Model\OptionSource\Campaign\NotificationType;
use Amasty\PushNotifications\Model\OptionSource\Campaign\Status;
use Amasty\PushNotifications\Model\ResourceModel\Campaign as CampaignResource;
use Amasty\PushNotifications\Model\ResourceModel\Campaign\Collection;
use Amasty\PushNotifications\Model\ResourceModel\CampaignStore\CollectionFactory as StoreCollectionFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Campaign extends AbstractExtensibleModel implements CampaignInterface, IdentityInterface
{
    const CACHE_TAG = 'amasty_notifications_campaign';

    /**
     * @var string
     */
    protected $_cacheTag = true;

    private $storeCollectionFactory;

    /**
     * @var array
     */
    private $subscriberViews = [];

    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        StoreCollectionFactory $storeCollectionFactory,
        CampaignResource $resource,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
        $this->storeCollectionFactory = $storeCollectionFactory;
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\Campaign::class);
        $this->setIdFieldName(CampaignInterface::CAMPAIGN_ID);
    }

    /**
     * Get identities for cache
     *
     * @return array
     */
    public function getIdentities()
    {
        return [Collection::CACHE_TAG, self::CACHE_TAG . '_' . $this->getCampaignId()];
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
    public function getCampaignId()
    {
        return $this->_getData(CampaignInterface::CAMPAIGN_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCampaignId($campaignId)
    {
        $this->setData(CampaignInterface::CAMPAIGN_ID, $campaignId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->_getData(CampaignInterface::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->setData(CampaignInterface::NAME, $name);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getNotificationType()
    {
        return $this->_getData(CampaignInterface::NOTIFICATION_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setNotificationType($notificationType)
    {
        $this->setData(CampaignInterface::NOTIFICATION_TYPE, $notificationType);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getScheduled()
    {
        return $this->_getData(CampaignInterface::SCHEDULED);
    }

    /**
     * @inheritdoc
     */
    public function setScheduled($scheduled)
    {
        $this->setData(CampaignInterface::SCHEDULED, $scheduled);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsActive()
    {
        return $this->_getData(CampaignInterface::IS_ACTIVE);
    }

    /**
     * @inheritdoc
     */
    public function setIsActive($active)
    {
        $this->setData(CampaignInterface::IS_ACTIVE, $active);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->_getData(CampaignInterface::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->setData(CampaignInterface::STATUS, $status);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSentCounter()
    {
        return (int)$this->_getData(CampaignInterface::SENT_COUNTER);
    }

    /**
     * @inheritdoc
     */
    public function setSentCounter($sentCounter)
    {
        $this->setData(CampaignInterface::SENT_COUNTER, $sentCounter);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getShownCounter()
    {
        return (int)$this->_getData(CampaignInterface::SHOWN_COUNTER);
    }

    /**
     * @inheritdoc
     */
    public function setShownCounter($shownCounter)
    {
        $this->setData(CampaignInterface::SHOWN_COUNTER, $shownCounter);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getShownUniqueCounter()
    {
        return (int)$this->_getData(CampaignInterface::SHOWN_UNIQUE_COUNTER);
    }

    /**
     * @inheritdoc
     */
    public function setShownUniqueCounter($shownUniqueCounter)
    {
        $this->setData(CampaignInterface::SHOWN_UNIQUE_COUNTER, $shownUniqueCounter);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getClickedCounter()
    {
        return (int)$this->_getData(CampaignInterface::CLICKED_COUNTER);
    }

    /**
     * @inheritdoc
     */
    public function setClickedCounter($clickedCounter)
    {
        $this->setData(CampaignInterface::CLICKED_COUNTER, $clickedCounter);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLogoPath()
    {
        return $this->_getData(CampaignInterface::LOGO_PATH);
    }

    /**
     * @inheritdoc
     */
    public function setLogoPath($logoPath)
    {
        $this->setData(CampaignInterface::LOGO_PATH, $logoPath);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsDefaultLogo()
    {
        return $this->_getData(CampaignInterface::IS_DEFAULT_LOGO);
    }

    /**
     * @inheritdoc
     */
    public function setIsDefaultLogo($isDefaultLogo)
    {
        $this->setData(CampaignInterface::IS_DEFAULT_LOGO, $isDefaultLogo);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMessageTitle()
    {
        return $this->_getData(CampaignInterface::MESSAGE_TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setMessageTitle($messageTitle)
    {
        $this->setData(CampaignInterface::MESSAGE_TITLE, $messageTitle);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMessageBody()
    {
        return $this->_getData(CampaignInterface::MESSAGE_TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setMessageBody($messageBody)
    {
        $this->setData(CampaignInterface::MESSAGE_BODY, $messageBody);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getButtonNotificationEnable()
    {
        return $this->_getData(CampaignInterface::BUTTON_NOTIFICATION_ENABLE);
    }

    /**
     * @inheritdoc
     */
    public function setButtonNotificationEnable($buttonNotificationEnable)
    {
        $this->setData(CampaignInterface::BUTTON_NOTIFICATION_ENABLE, $buttonNotificationEnable);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getButtonNotificationText()
    {
        return $this->_getData(CampaignInterface::BUTTON_NOTIFICATION_TEXT);
    }

    /**
     * @inheritdoc
     */
    public function setButtonNotificationText($buttonNotificationText)
    {
        $this->setData(CampaignInterface::BUTTON_NOTIFICATION_TEXT, $buttonNotificationText);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getButtonNotificationUrl()
    {
        return $this->_getData(CampaignInterface::BUTTON_NOTIFICATION_URL);
    }

    /**
     * @inheritdoc
     */
    public function setButtonNotificationUrl($buttonNotificationUrl)
    {
        $this->setData(CampaignInterface::BUTTON_NOTIFICATION_URL, $buttonNotificationUrl);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUtmParams()
    {
        return $this->_getData(CampaignInterface::UTM_PARAMS);
    }

    /**
     * @inheritdoc
     */
    public function setUtmParams($utmParams)
    {
        $this->setData(CampaignInterface::UTM_PARAMS, $utmParams);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->_getData(CampaignInterface::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(CampaignInterface::UPDATED_AT, $updatedAt);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->_getData(CampaignInterface::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(CampaignInterface::CREATED_AT, $createdAt);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSegmentationSource()
    {
        return (int)$this->_getData(CampaignInterface::SEGMENTATION_SOURCE);
    }

    /**
     * @inheritdoc
     */
    public function setSegmentationSource($source)
    {
        $this->setData(CampaignInterface::SEGMENTATION_SOURCE, (int)$source);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setCustomerGroups($groups)
    {
        $this->setData(CampaignInterface::CUSTOMER_GROUPS, $groups);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomerGroups()
    {
        return (array)$this->_getData(CampaignInterface::CUSTOMER_GROUPS);
    }

    /**
     * @inheritdoc
     */
    public function setSegments($segments)
    {
        $this->setData(CampaignInterface::CUSTOMER_SEGMENTS, $segments);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSegments()
    {
        return (array)$this->_getData(CampaignInterface::CUSTOMER_SEGMENTS);
    }

    /**
     * @inheritdoc
     */
    public function getEvents()
    {
        return (array)$this->_getData(CampaignInterface::EVENTS);
    }

    /**
     * @inheritdoc
     */
    public function setEvents($events)
    {
        $this->setData(CampaignInterface::EVENTS, $events);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function processCampaign()
    {
        switch ($this->getNotificationType()) {
            case NotificationType::EVENT_TYPE:
                $this->setStatus(Status::STATUS_BY_EVENT);
                break;
            case NotificationType::CRON_TYPE:
                $this->setStatus(Status::STATUS_PASSED)
                    ->setIsActive(Active::STATUS_INACTIVE);
                break;
        }

        $this->_resource->save($this);

        return $this;
    }

    /**
     * @return array
     */
    public function getStores()
    {
        if ($this->getData(self::STORES) == null) {
            $this->setData(
                self::STORES,
                $this->getStoresCollection()->getItems()
            );
        }
        return $this->getData(self::STORES);
    }

//    /**
//     * @return mixed
//     */
//    public function getStoreIds()
//    {
//        return $this->getResource()->getStoreIds($this->getId());
//    }

    /**
     * @return ResourceModel\CampaignStore\Collection
     */
    public function getStoresCollection()
    {
        $collection = $this->storeCollectionFactory->create()->addFieldToFilter('campaign_id', $this->getId());
        if ($this->getId()) {
            /** @var \Amasty\PushNotifications\Model\CampaignStore $item */
            foreach ($collection as $item) {
                $item->setCampaign($this);
            }
        }
        return $collection;
    }

    /**
     * @param CampaignStore $store
     * @return $this
     */
    public function addStore(CampaignStore $store)
    {
        $store->setCampaign($this);
        if (!$store->getId()) {
            $this->setData(self::STORES, array_merge($this->getStores(), [$store]));
        }

        return $this;
    }

    /**
     * @param null $storeId
     * @return $this
     *
     * @throws \Exception
     */
    public function deleteStore($storeId = null)
    {
        $stores = $this->storeCollectionFactory->create()->addFieldToFilter('campaign_id', $this->getId());

        if ($storeId) {
            $stores = $stores->addFieldToFilter('store_id', $storeId);
        }

        /** @var \Amasty\PushNotifications\Model\CampaignStore $item */
        foreach ($stores as $item) {
            $item->delete();
        }
        $this->setData(
            self::STORES,
            $this->getStoresCollection()->getItems()
        );

        return $this;
    }

    public function addSubscriberViews(array $subscriberViews)
    {
        $this->subscriberViews = array_merge($this->subscriberViews, $subscriberViews);

        return $this;
    }

    public function getSubscriberViews(): array
    {
        return $this->subscriberViews;
    }
}
