<?php

namespace Amasty\Checkout\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class DisplayAgreements
 */
class DisplayAgreements implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'payment_method', 'label' => __('Below the Selected Payment Method')],
            ['value' => 'order_totals', 'label' => __('Below the Order Total')]
        ];
    }
}
