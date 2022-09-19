<?php

namespace Amasty\Checkout\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class MultipleShippingAddressOptions
 */
class MultipleShippingAddressOptions implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Grid'),
                'value' => 0
            ],
            [
                'label' => __('Dropdown Menu'),
                'value' => 1
            ]
        ];
    }
}
