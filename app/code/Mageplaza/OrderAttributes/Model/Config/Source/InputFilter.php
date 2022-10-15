<?php

namespace Mageplaza\OrderAttributes\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class InputFilter
 * @package Mageplaza\OrderAttributes\Model\Config\Source
 */
class InputFilter implements ArrayInterface
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
            ['value' => 'striptags', 'label' => __('Strip HTML Tags')],
            ['value' => 'escapehtml', 'label' => __('Escape HTML Entities')]
        ];
    }
}
