<?php

namespace Amasty\PushNotifications\Model\Config\Source;

class PromptFrequency implements \Magento\Framework\Data\OptionSourceInterface
{
    const FREQUENCY_EVERY_TIME = 0;
    const FREQUENCY_HOURLY     = 1;
    const FREQUENCY_DAILY  = 2;
    const FREQUENCY_WEEKLY  = 3;

    /**
     * @var array|null
     */
    protected $options;

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                self::FREQUENCY_EVERY_TIME  => __('Every time'),
                self::FREQUENCY_HOURLY      => __('Hourly'),
                self::FREQUENCY_DAILY   => __('Daily'),
                self::FREQUENCY_WEEKLY   => __('Weekly'),
            ];
        }

        return $this->options;
    }
}
