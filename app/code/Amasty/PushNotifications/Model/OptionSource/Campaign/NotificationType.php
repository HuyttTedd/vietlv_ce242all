<?php
declare(strict_types=1);

namespace Amasty\PushNotifications\Model\OptionSource\Campaign;

use Magento\Framework\Data\OptionSourceInterface;

class NotificationType implements OptionSourceInterface
{
    const CRON_TYPE = 'cron';
    const EVENT_TYPE = 'event';

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::CRON_TYPE,
                'label' => __('Scheduled Notification')
            ],
            [
                'value' => self::EVENT_TYPE,
                'label' => __('Event Notification')
            ]
        ];
    }
}
