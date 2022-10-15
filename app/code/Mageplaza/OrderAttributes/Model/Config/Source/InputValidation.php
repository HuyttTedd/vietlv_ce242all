<?php

namespace Mageplaza\OrderAttributes\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class InputValidation
 * @package Mageplaza\OrderAttributes\Model\Config\Source
 */
class InputValidation implements ArrayInterface
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
            ['value' => 'alphanumeric', 'label' => __('Letters (a-z, A-Z) or Numbers (0-9)')],
            ['value' => 'numeric', 'label' => __('Numbers')],
            ['value' => 'alpha', 'label' => __('Letters')],
            ['value' => 'url', 'label' => __('URL')],
            ['value' => 'email', 'label' => __('Email')]
        ];
    }
}
