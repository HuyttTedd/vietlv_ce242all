<?php

namespace Amasty\PushNotifications\Model\Builder;

use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Amasty\PushNotifications\Controller\RegistryConstants;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Registry;
use Magento\Framework\Url;

class UrlBuilder implements BuilderInterface
{
    const URL_SLASH = '/';

    /**
     * @var Url
     */
    private $url;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Url $url,
        EncryptorInterface $encryptor,
        Registry $registry
    ) {
        $this->url = $url;
        $this->encryptor = $encryptor;
        $this->registry = $registry;
    }

    /**
     * @inheritdoc
     */
    public function build(array $params)
    {
        $url = $params[CampaignInterface::BUTTON_NOTIFICATION_URL];

        if (!$url) {
            $url = self::URL_SLASH;
        } else {
            $url = $url == self::URL_SLASH ? $url : rtrim($url, self::URL_SLASH);
        }

        if ($this->registry->registry(RegistryConstants::REGISTRY_TEST_NOTIFICATION_NAME)) {
            return $url;
        }

        return $url . '?'
            . $this->buildNotificationClickCounterGetParams($params[CampaignInterface::CAMPAIGN_ID])
            . '&' . $params[CampaignInterface::UTM_PARAMS];
    }

    /**
     * @param int $campaignId
     *
     * @return string
     */
    public function buildNotificationClickCounterUrl($campaignId)
    {
        return $this->url->getUrl(RegistryConstants::FIREBASE_CLICK_COUNTER_URL_PATH, ['campaignId' => $campaignId]);
    }

    /**
     * @param int $campaignId
     *
     * @return string
     */
    public function buildNotificationClickCounterGetParams($campaignId)
    {
        $params = [
            RegistryConstants::CLICK_COUNTER_FLAG_PARAM_NAME => 1,
            RegistryConstants::CAMPAIGN_ID_PARAMS_KEY_NAME   => $this->encryptor->encrypt((string)$campaignId)
        ];

        return http_build_query($params);
    }
}
