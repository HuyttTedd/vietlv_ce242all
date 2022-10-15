<?php

namespace Mageplaza\OrderAttributes\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Mageplaza\OrderAttributes\Helper\Data;

/**
 * Class InputType
 * @package Mageplaza\OrderAttributes\Model\Config\Source
 */
class InputType implements ArrayInterface
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @param Data $dataHelper
     */
    public function __construct(Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $inputTypes = $this->dataHelper->getInputType();
        $options = [];
        foreach ($inputTypes as $key => $value) {
            $options[] = ['value' => $key, 'label' => $value['label']];
        }

        return $options;
    }
}
