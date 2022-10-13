<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_OrderAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\OrderAttributes\Ui\Component\Listing;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\CollectionFactory;
use Mageplaza\OrderAttributes\Ui\Component\ColumnFactory;

/**
 * Class Columns
 * @package Mageplaza\OrderAttributes\Ui\Component\Listing
 */
class Columns extends \Magento\Ui\Component\Listing\Columns
{
    /**
     * Default columns max order
     */
    const DEFAULT_COLUMNS_MAX_ORDER = 100;

    /**
     * @var int
     */
    protected $columnSortOrder;

    /**
     * @var array
     */
    protected $filterMap = [
        'default' => 'text',
        'boolean' => 'select',
        'select' => 'select',
        'select_visual' => 'select',
        'multiselect' => 'select',
        'multiselect_visual' => 'select',
        'date' => 'dateRange',
    ];

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var ColumnFactory
     */
    protected $columnFactory;

    /**
     * @var CollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * Columns constructor.
     *
     * @param ContextInterface $context
     * @param Data $helperData
     * @param ColumnFactory $columnFactory
     * @param CollectionFactory $attributeCollectionFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        Data $helperData,
        ColumnFactory $columnFactory,
        CollectionFactory $attributeCollectionFactory,
        array $components = [],
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->columnFactory = $columnFactory;
        $this->attributeCollectionFactory = $attributeCollectionFactory;

        parent::__construct($context, $components, $data);
    }

    /**
     * @return array
     */
    protected function getAttributeList()
    {
        if (!$this->helperData->isEnabled()) {
            return [];
        }

        $attributes = $this->attributeCollectionFactory->create();

        $result = [];
        foreach ($attributes as $attribute) {
            if (!$attribute->getIsUsedInGrid()) {
                continue;
            }

            $result[] = $attribute;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $columnSortOrder = self::DEFAULT_COLUMNS_MAX_ORDER;
        foreach ($this->getAttributeList() as $attribute) {
            $config = [];
            if (!isset($this->components[$attribute->getAttributeCode()])) {
                $config['sortOrder'] = ++$columnSortOrder;
                $config['filter'] = $this->getFilterType($attribute->getFrontendInput());
                $column = $this->columnFactory->create($attribute, $this->getContext(), $config);
                $column->prepare();
                $this->addComponent($attribute->getAttributeCode(), $column);
            }
        }
        parent::prepare();
    }

    /**
     * Retrieve filter type by $frontendInput
     *
     * @param string $frontendInput
     *
     * @return string
     */
    protected function getFilterType($frontendInput)
    {
        return isset($this->filterMap[$frontendInput]) ? $this->filterMap[$frontendInput] : $this->filterMap['default'];
    }
}
