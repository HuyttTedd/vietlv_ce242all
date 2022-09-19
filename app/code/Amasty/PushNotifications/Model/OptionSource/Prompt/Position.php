<?php

namespace Amasty\PushNotifications\Model\OptionSource\Prompt;

use Magento\Framework\Data\OptionSourceInterface;

class Position implements OptionSourceInterface
{
    const STATUS_RIGHT = 'right';
    const STATUS_LEFT = 'left';
    const STATUS_CENTER = 'center';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::STATUS_RIGHT, 'label'=> __('Bottom Right')],
            ['value' => self::STATUS_LEFT, 'label'=> __('Bottom Left')],
            ['value' => self::STATUS_CENTER, 'label'=> __('Bottom Center')]
        ];
    }
}
