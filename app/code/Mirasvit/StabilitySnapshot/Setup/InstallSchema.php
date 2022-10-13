<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-stability
 * @version   1.1.0
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\StabilitySnapshot\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\StabilitySnapshot\Api\Data\SnapshotInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer  = $setup;
        $connection = $installer->getConnection();

        $installer->startSetup();

        if ($connection->isTableExists($installer->getTable(SnapshotInterface::TABLE_NAME))) {
            $connection->dropTable($installer->getTable(SnapshotInterface::TABLE_NAME));
        }

        $table = $connection->newTable(
            $installer->getTable(SnapshotInterface::TABLE_NAME)
        )->addColumn(
            SnapshotInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            SnapshotInterface::ID
        )->addColumn(
            SnapshotInterface::STATUS,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            SnapshotInterface::STATUS
        )->addColumn(
            SnapshotInterface::ENVIRONMENT_DATA,
            Table::TYPE_TEXT,
            '10240k',
            ['nullable' => false],
            SnapshotInterface::ENVIRONMENT_DATA
        )->addColumn(
            SnapshotInterface::ENVIRONMENT_HASH,
            Table::TYPE_TEXT,
            '255',
            ['nullable' => false],
            SnapshotInterface::ENVIRONMENT_HASH
        )->addColumn(
            SnapshotInterface::HEALTH_DATA,
            Table::TYPE_TEXT,
            '10240k',
            ['nullable' => true],
            SnapshotInterface::HEALTH_DATA
        )->addColumn(
            SnapshotInterface::NOTE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            SnapshotInterface::NOTE
        )->addColumn(
            SnapshotInterface::CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            SnapshotInterface::CREATED_AT
        )->addColumn(
            SnapshotInterface::CLOSED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            SnapshotInterface::CLOSED_AT
        )->addColumn(
            SnapshotInterface::UPDATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            SnapshotInterface::UPDATED_AT
        );
        $connection->createTable($table);
    }
}
