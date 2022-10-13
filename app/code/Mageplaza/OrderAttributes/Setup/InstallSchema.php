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

namespace Mageplaza\OrderAttributes\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package Mageplaza\OrderAttributes\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $connection = $setup->getConnection();

        if (!$setup->tableExists('mageplaza_order_attribute')) {
            $table = $setup->getConnection()
                ->newTable($setup->getTable('mageplaza_order_attribute'))
                ->addColumn('attribute_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true
                ], 'Attribute Id')
                ->addColumn(
                    'attribute_code',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'default' => null],
                    'Attribute Code'
                )
                ->addColumn(
                    'backend_type',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => 'static'],
                    'Backend Type'
                )
                ->addColumn('frontend_input', Table::TYPE_TEXT, 50, [], 'Frontend Input')
                ->addColumn('frontend_label', Table::TYPE_TEXT, 255, [], 'Frontend Label')
                ->addColumn(
                    'is_required',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Is Required'
                )
                ->addColumn('default_value', Table::TYPE_TEXT, '1M', [], 'Default Value')
                ->addColumn('input_filter', Table::TYPE_TEXT, 255, [], 'Input Filter')
                ->addColumn('frontend_class', Table::TYPE_TEXT, 50, [], 'Frontend Class')
                ->addColumn(
                    'sort_order',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Sort Order'
                )
                ->addColumn('is_used_in_grid', Table::TYPE_SMALLINT, null, [], 'Is Used In Sales Order Grid')
                ->addColumn('show_in_frontend_order', Table::TYPE_SMALLINT, null, [], 'Add to Sales Order View')
                ->addColumn('field_depend', Table::TYPE_INTEGER, null, [], 'Field to depend on')
                ->addColumn('value_depend', Table::TYPE_TEXT, 255, [], 'Value to depend on')
                ->addColumn('shipping_depend', Table::TYPE_TEXT, 255, [], 'Shipping to depend on')
                ->addColumn('store_id', Table::TYPE_TEXT, 255, ['nullable' => false], 'Store Id')
                ->addColumn('customer_group', Table::TYPE_TEXT, 255, ['nullable' => false], 'Customer Group')
                ->addColumn('position', Table::TYPE_TEXT, 50, [], 'Position')
                ->addColumn('use_tooltip', Table::TYPE_SMALLINT, null, [], 'Use Tooltip')
                ->addColumn('additional_data', Table::TYPE_TEXT, '1M', [], 'Additional Data')
                ->addColumn('labels', Table::TYPE_TEXT, '1M', [], 'Labels')
                ->addColumn('tooltips', Table::TYPE_TEXT, '1M', [], 'Tooltips')
                ->addColumn('options', Table::TYPE_TEXT, '2M', [], 'Options')
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => Table::TIMESTAMP_INIT],
                    'Update At'
                )
                ->setComment('Order Attribute Table');

            $connection->createTable($table);
        }

        $table = $connection->newTable(
            $setup->getTable('mageplaza_order_attribute_sales_order')
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
            'Entity Id'
        )->addForeignKey(
            $setup->getFkName(
                'mageplaza_order_attribute_sales_order',
                'entity_id',
                'sales_order',
                'entity_id'
            ),
            'entity_id',
            $setup->getTable('sales_order'),
            'entity_id',
            Table::ACTION_CASCADE
        )->setComment('Order Attribute Sales Order');
        $connection->createTable($table);

        $table = $connection->newTable(
            $setup->getTable('mageplaza_order_attribute_quote')
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
            'Entity Id'
        )->addForeignKey(
            $setup->getFkName(
                'mageplaza_order_attribute_quote',
                'entity_id',
                'quote',
                'entity_id'
            ),
            'entity_id',
            $setup->getTable('quote'),
            'entity_id',
            Table::ACTION_CASCADE
        )->setComment('Order Attribute Quote');
        $connection->createTable($table);

        $setup->endSetup();
    }
}
