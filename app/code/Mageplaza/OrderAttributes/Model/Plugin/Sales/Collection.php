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

namespace Mageplaza\OrderAttributes\Model\Plugin\Sales;

use Magento\Sales\Model\ResourceModel\Order\Grid;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OrderCollection;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\CollectionFactory;

/**
 * Class Collection
 * @package Mageplaza\OrderAttributes\Model\Plugin\Sales
 */
class Collection
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param OrderCollection $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterGetSelect(
        OrderCollection $subject,
        $result
    ) {
        if ($result && !$subject->getFlag('is_mageplaza_order_attribute_sales_order_joined')) {
            $table = $subject->getResource()->getTable('mageplaza_order_attribute_sales_order');
            $result->joinLeft(
                ['mp_order_attributes' => $table],
                'mp_order_attributes.order_id = main_table.entity_id'
            );
            $tableDescription = $subject->getConnection()->describeTable($table);
            foreach ($tableDescription as $columnInfo) {
                $subject->addFilterToMap(
                    $columnInfo['COLUMN_NAME'],
                    'mp_order_attributes.' . $columnInfo['COLUMN_NAME']
                );
            }
            $subject->setFlag('is_mageplaza_order_attribute_sales_order_joined', true);
        }

        return $result;
    }

    /**
     * @param Grid\Collection $subject
     * @param $field
     * @param null $condition
     *
     * @return array
     */
    public function beforeAddFieldToFilter(Grid\Collection $subject, $field, $condition = null)
    {
        $collection = $this->collectionFactory->create();
        $attribute = $collection->addFieldToFilter('attribute_code', $field)->fetchItem();

        if (!$attribute) {
            return [$field, $condition];
        }

        if (isset($condition['eq']) && strpos($attribute->getFrontendInput(), 'multiselect') !== false) {
            $condition = ['like' => '%' . $condition['eq'] . '%'];
        }

        return [$field, $condition];
    }
}
