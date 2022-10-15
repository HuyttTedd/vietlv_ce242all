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



namespace Mirasvit\StabilityError\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\Stability\Api\Data\SnapshotInterface;
use Mirasvit\Stability\Api\Data\TimingInterface;
use Mirasvit\StabilityError\Api\Data\ErrorInterface;


class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer  = $setup;
        $connection = $installer->getConnection();

        $installer->startSetup();

        if ($connection->isTableExists($installer->getTable(ErrorInterface::TABLE_NAME))) {
            $connection->dropTable($installer->getTable(ErrorInterface::TABLE_NAME));
        }

        $table = $connection->newTable(
            $installer->getTable(ErrorInterface::TABLE_NAME)
        )->addColumn(
            ErrorInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            ErrorInterface::ID
        )->addColumn(
            ErrorInterface::SNAPSHOT_ID,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            ErrorInterface::SNAPSHOT_ID
        )->addColumn(
            ErrorInterface::IDENTIFIER,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            ErrorInterface::IDENTIFIER
        )->addColumn(
            ErrorInterface::TYPE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            ErrorInterface::TYPE
        )->addColumn(
            ErrorInterface::URI,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            ErrorInterface::URI
        )->addColumn(
            ErrorInterface::MESSAGE,
            Table::TYPE_TEXT,
            1024,
            ['nullable' => true],
            ErrorInterface::MESSAGE
        )->addColumn(
            ErrorInterface::TRACE,
            Table::TYPE_TEXT,
            1024 * 1024,
            ['nullable' => true],
            ErrorInterface::TRACE
        )->addColumn(
            ErrorInterface::COUNT,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 1],
            ErrorInterface::COUNT
        )->addColumn(
            ErrorInterface::CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            ErrorInterface::CREATED_AT
        )->addColumn(
            ErrorInterface::UPDATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            ErrorInterface::UPDATED_AT
        );
        $connection->createTable($table);
    }
}
