<?php

namespace Mageplaza\OrderAttributes\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class FrontendClass
 * @package Mageplaza\OrderAttributes\Model\Config\Source
 */
class FrontendClass implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('None')],
            ['value' => 'validate-number', 'label' => __('Decimal Number')],
            ['value' => 'validate-digits', 'label' => __('Integer Number')],
            ['value' => 'validate-email', 'label' => __('Email')],
            ['value' => 'validate-url', 'label' => __('URL')],
            ['value' => 'validate-alpha', 'label' => __('Letters')],
            ['value' => 'validate-alphanum', 'label' => __('Letters (a-z, A-Z) or Numbers (0-9)')],
            [
                'value' => 'validate-alphanum-with-spaces',
                'label' => __('Letters (a-z, A-Z) or Numbers (0-9) With Spaces')
            ]
        ];
    }
}
