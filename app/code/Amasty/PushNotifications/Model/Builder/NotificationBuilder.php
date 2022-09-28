<?php

namespace Amasty\PushNotifications\Model\Builder;

use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Amasty\PushNotifications\Exception\NotificationException;
use Amasty\PushNotifications\Model\Builder\LogoUrlBuilder;
use Amasty\PushNotifications\Model\Builder\UrlBuilder;

class NotificationBuilder implements BuilderInterface
{
    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @var \Amasty\PushNotifications\Model\Builder\LogoUrlBuilder
     */
    private $logoUrlBuilder;

    public function __construct(
        UrlBuilder $urlBuilder,
        LogoUrlBuilder $logoUrlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->logoUrlBuilder = $logoUrlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function build(array $params)
    {
        if ($params) {
            return $this->prepareNotificationBodyFromCampaignData($params);
        }

        throw new NotificationException(__('Campaign data not found'));
    }

    /**
     * @param array $campaignData
     *
     * @return array
     */
    private function prepareNotificationBodyFromCampaignData(array $campaignData)
    {
        $body = [
            'title'             => $campaignData[CampaignInterface::MESSAGE_TITLE],
            'body'              => $campaignData[CampaignInterface::MESSAGE_BODY],
            'icon'              => $this->getNotificationLogo($campaignData),
            'click_action'      => $this->getNotificationLink($campaignData),
        ];

        return $body;
    }

    /**
     * @param array $campaignData
     *
     * @return string
     */
    private function getNotificationLogo(array $campaignData)
    {
        return $this->logoUrlBuilder->build($campaignData);
    }

    /**
     * @param array $campaignData
     *
     * @return array|string
     */
    private function getNotificationLink(array $campaignData)
    {
        $link = '#';

        /**TODO add new feature with checkbox and notification context button */
        //        if ($campaignData[CampaignInterface::BUTTON_NOTIFICATION_ENABLE]) {
        $link = $this->urlBuilder->build($campaignData);
//        }

        return $link;
    }
}
