<?php

namespace Amasty\Checkout\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CheckoutDesign
 */
class CheckoutDesign implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Classic')],
            ['value' => 1, 'label' => __('Modern')]
        ];
    }
}
