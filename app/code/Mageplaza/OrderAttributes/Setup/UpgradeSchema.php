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
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Mageplaza\OrderAttributes\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();
        $orderAttributeTable = $setup->getTable('mageplaza_order_attribute');
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $connection->addColumn($orderAttributeTable, 'max_file_size', [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Maximum File Size'
            ]);
            $connection->addColumn($orderAttributeTable, 'allow_extensions', [
                'type' => Table::TYPE_TEXT,
                'size' => 255,
                'nullable' => true,
                'comment' => 'Allow extensions'
            ]);
        }

        $moaso = $setup->getTable('mageplaza_order_attribute_sales_order');
        if (($connection->tableColumnExists($moaso, 'entity_id'))) {
            $connection->changeColumn(
                $moaso,
                'entity_id',
                'order_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'default' => '0'
                ]
            );
        }

        if ($connection->tableColumnExists($orderAttributeTable, 'shipping_depend')) {
            $connection->modifyColumn(
                $orderAttributeTable,
                'shipping_depend',
                ['type' => Table::TYPE_TEXT, 'size' => '1M']
            );
            $connection->modifyColumn(
                $orderAttributeTable,
                'sort_order',
                ['type' => Table::TYPE_SMALLINT]
            );
        }

        $setup->endSetup();
    }
}
