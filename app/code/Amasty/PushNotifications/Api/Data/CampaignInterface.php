<?php

namespace Amasty\PushNotifications\Api\Data;

interface CampaignInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const CAMPAIGN_ID = 'campaign_id';
    const NAME = 'name';
    const NOTIFICATION_TYPE = 'notification_type';
    const SCHEDULED = 'scheduled';
    const IS_ACTIVE = 'is_active';
    const STATUS = 'status';
    const SENT_COUNTER = 'sent';
    const SHOWN_COUNTER = 'shown';
    const SHOWN_UNIQUE_COUNTER = 'shown_unique';
    const LOGO_PATH = 'logo_path';
    const IS_DEFAULT_LOGO = 'is_default_logo';
    const MESSAGE_TITLE = 'message_title';
    const MESSAGE_BODY = 'message_body';
    const BUTTON_NOTIFICATION_ENABLE = 'button_notification_enable';
    const BUTTON_NOTIFICATION_TEXT = 'button_notification_text';
    const BUTTON_NOTIFICATION_URL = 'button_notification_url';
    const CLICKED_COUNTER = 'clicked';
    const UTM_PARAMS = 'utm_params';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const STORES = 'stores';
    const STORE_ID = 'store_id';
    const SEGMENTATION_SOURCE = 'segmentation_source';
    const CUSTOMER_GROUPS = 'customer_groups';
    const CUSTOMER_SEGMENTS = 'customer_segments';
    const EVENTS = 'events';
    /**#@-*/

    /**
     * @return int
     */
    public function getCampaignId();

    /**
     * @param int $campaignId
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setCampaignId($campaignId);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getNotificationType();

    /**
     * @param string $notificationType
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setNotificationType($notificationType);

    /**
     * @return string
     */
    public function getScheduled();

    /**
     * @param string $scheduled
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setScheduled($scheduled);

    /**
     * @return int|string
     */
    public function getIsActive();

    /**
     * @param int|string $active
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setIsActive($active);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setStatus($status);

    /**
     * @return int
     */
    public function getSentCounter();

    /**
     * @param int|string $sentCounter
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setSentCounter($sentCounter);

    /**
     * @return int
     */
    public function getShownCounter();

    /**
     * @param int|string $shownCounter
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setShownCounter($shownCounter);

    /**
     * @return int
     */
    public function getShownUniqueCounter();

    /**
     * @param int|string $shownUniqueCounter
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setShownUniqueCounter($shownUniqueCounter);

    /**
     * @return int
     */
    public function getClickedCounter();

    /**
     * @param int|string $clickedCounter
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setClickedCounter($clickedCounter);

    /**
     * @return string
     */
    public function getLogoPath();

    /**
     * @param string $logoPath
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setLogoPath($logoPath);

    /**
     * @return int
     */
    public function getIsDefaultLogo();

    /**
     * @param int|string $isDefaultLogo
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setIsDefaultLogo($isDefaultLogo);

    /**
     * @return string
     */
    public function getMessageTitle();

    /**
     * @param string $messageTitle
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setMessageTitle($messageTitle);

    /**
     * @return string
     */
    public function getMessageBody();

    /**
     * @param string $messageBody
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setMessageBody($messageBody);

    /**
     * @return int
     */
    public function getButtonNotificationEnable();

    /**
     * @param int|string $buttonNotificationEnable
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setButtonNotificationEnable($buttonNotificationEnable);

    /**
     * @return string
     */
    public function getButtonNotificationText();

    /**
     * @param string $buttonNotificationText
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setButtonNotificationText($buttonNotificationText);

    /**
     * @return string
     */
    public function getButtonNotificationUrl();

    /**
     * @param string $buttonNotificationUrl
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setButtonNotificationUrl($buttonNotificationUrl);

    /**
     * @return string
     */
    public function getUtmParams();

    /**
     * @param string $utmParams
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setUtmParams($utmParams);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $createdAt
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setUpdatedAt($createdAt);

    /**
     * @return int
     */
    public function getSegmentationSource();

    /**
     * @param int $source
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setSegmentationSource($source);

    /**
     * @return \Amasty\PushNotifications\Model\CampaignCustomerGroup[]
     */
    public function getCustomerGroups();

    /**
     * @param \Amasty\PushNotifications\Model\CampaignCustomerGroup[] $groups
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setCustomerGroups($groups);

    /**
     * @return \Amasty\PushNotifications\Model\CampaignSegments[]
     */
    public function getSegments();

    /**
     * @param \Amasty\PushNotifications\Model\CampaignSegments[] $segments
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setSegments($segments);

    /**
     * @return string[]
     */
    public function getEvents();

    /**
     * @param string[] $events
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     */
    public function setEvents($events);

    /**
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     *
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function processCampaign();
}
