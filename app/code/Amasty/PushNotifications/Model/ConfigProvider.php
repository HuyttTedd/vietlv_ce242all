<?php

namespace Amasty\PushNotifications\Model;

use Amasty\Base\Model\ConfigProviderAbstract;

/**
 * Scope config Provider model
 */
class ConfigProvider extends ConfigProviderAbstract
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    const XPATH_ENABLED = 'general/enable';
    const XPATH_SERVER_ID = 'general/sender_id';
    const XPATH_API_KEY = 'general/api_key';
    const XPATH_DESIGN_LOGO = 'design/logo';
    const XPATH_CUSTOM_PROMPT_ENABLE = 'prompt/prompt_enable';
    const XPATH_CUSTOM_PROMPT_DEVICE = 'prompt/device';
    const XPATH_CUSTOM_PROMPT_TEXT = 'prompt/text';
    const XPATH_CUSTOM_PROMPT_POSITION = 'prompt/position';
    const XPATH_CUSTOM_PROMPT_DELAY = 'prompt/delay';
    const XPATH_CUSTOM_PROMPT_FREQUENCY = 'prompt/frequency';
    const XPATH_CUSTOM_PROMPT_ALL_PAGES = 'prompt/all_pages';
    const XPATH_CUSTOM_PROMPT_PAGES = 'prompt/pages';
    const XPATH_MAX_NOTIFICATIONS_PER_CUSTOMER_DAILY = 'no_spam/max_limit';
    const XPATH_EXPIRE_NOTIFICATIONS = 'no_spam/expire_days';
    const FIREBASE_API_REQUEST_URL = 'https://fcm.googleapis.com/fcm/send';
    /**#@-*/

    /**
     * xpath prefix of module (section)
     * @var string '{section}/'
     */
    protected $pathPrefix = 'amasty_notifications/';

    /**
     * @return boolean
     */
    public function isModuleEnable()
    {
        return (bool)$this->getGlobalValue(self::XPATH_ENABLED);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getSenderId($storeId = null)
    {
        return $this->getValue(self::XPATH_SERVER_ID, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getFirebaseApiKey($storeId = null)
    {
        return $this->getValue(self::XPATH_API_KEY, $storeId);
    }

    /**
     * @return string
     */
    public function getLogoPath($storeId = null)
    {
        return $this->getValue(self::XPATH_DESIGN_LOGO, $storeId);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isCustomPromptEnable($storeId = null)
    {
        return (bool)$this->getValue(self::XPATH_CUSTOM_PROMPT_ENABLE, $storeId);
    }

    /**
     * @return array
     */
    public function getDevicesAvailableForPrompt(): array
    {
        $deviceTypes = (string)$this->getValue(self::XPATH_CUSTOM_PROMPT_DEVICE);

        return array_filter(explode(',', $deviceTypes));
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getCustomPromptText($storeId = null)
    {
        return $this->getValue(self::XPATH_CUSTOM_PROMPT_TEXT, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getCustomPromptPosition($storeId = null)
    {
        $configValue = $this->getValue(self::XPATH_CUSTOM_PROMPT_POSITION, $storeId);

        return $configValue ? (string)$configValue : 'right';
    }

    /**
     * @param null $storeId
     * @return int
     */
    public function getMaxNotificationsPerCustomerDaily($storeId = null)
    {
        $configValue = $this->getValue(self::XPATH_MAX_NOTIFICATIONS_PER_CUSTOMER_DAILY, $storeId);

        return $configValue ? (int)$configValue : 0;
    }

    /**
     * @param null $storeId
     * @return int
     */
    public function getExpireNotifications($storeId = null)
    {
        $configValue = $this->getValue(self::XPATH_EXPIRE_NOTIFICATIONS, $storeId);

        return $configValue ? (int)$configValue : 0;
    }

    /**
     * @param null $storeId
     * @return int
     */
    public function getCustomPromptDelay($storeId = null)
    {
        $configValue = $this->getValue(self::XPATH_CUSTOM_PROMPT_DELAY, $storeId);

        return $configValue ? (int)$configValue : 0;
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getCustomPromptFrequency($storeId = null)
    {
        $configValue = $this->getValue(self::XPATH_CUSTOM_PROMPT_FREQUENCY, $storeId);

        return $configValue ? (int)$configValue : 0;
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function getPromptAvailableOnAllPages($storeId = null)
    {
        return (bool)$this->getValue(self::XPATH_CUSTOM_PROMPT_ALL_PAGES, $storeId);
    }
    /**
     * @param null $storeId
     * @return array
     */
    public function getCustomPromptAvailablePages($storeId = null)
    {
        $ignore = $this->getValue(self::XPATH_CUSTOM_PROMPT_PAGES, $storeId);
        $ignoreList = preg_split('|[\r\n]+|', $ignore, -1, PREG_SPLIT_NO_EMPTY);

        return $ignoreList;
    }

    /**
     * @return string
     */
    public function getFirebaseApiRequestUrl()
    {
        return self::FIREBASE_API_REQUEST_URL;
    }

    /**
     * @return string
     */
    public function getPathPrefix()
    {
        return trim($this->pathPrefix, DIRECTORY_SEPARATOR);
    }
}
