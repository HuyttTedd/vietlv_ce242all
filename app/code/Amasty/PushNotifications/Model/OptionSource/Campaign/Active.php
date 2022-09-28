<?php

namespace Amasty\PushNotifications\Model\OptionSource\Campaign;

use Magento\Framework\Data\OptionSourceInterface;

class Active implements OptionSourceInterface
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            self::STATUS_ACTIVE => __("Active"),
            self::STATUS_INACTIVE => __("Inactive"),
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::STATUS_ACTIVE,
                'label' => __("Active")
            ],
            [
                'value' => self::STATUS_INACTIVE,
                'label' => __("Inactive")
            ],
        ];
    }
}
