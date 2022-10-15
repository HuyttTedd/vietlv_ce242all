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
 * @package   mirasvit/module-banner
 * @version   1.0.11
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Banner\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\Banner\Api\Data\AnalyticsInterface;
use Mirasvit\Banner\Api\Data\BannerInterface;
use Mirasvit\Banner\Api\Data\PlaceholderInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()->newTable(
            $installer->getTable(BannerInterface::TABLE_NAME)
        )->addColumn(
            BannerInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'identity' => true, 'primary' => true],
            BannerInterface::ID
        )->addColumn(
            BannerInterface::NAME,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            BannerInterface::NAME
        )->addColumn(
            BannerInterface::IS_ACTIVE,
            Table::TYPE_INTEGER,
            1,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            BannerInterface::IS_ACTIVE
        )->addColumn(
            BannerInterface::ACTIVE_FROM,
            Table::TYPE_DATETIME,
            null,
            ['nullable' => true],
            BannerInterface::ACTIVE_FROM
        )->addColumn(
            BannerInterface::ACTIVE_TO,
            Table::TYPE_DATETIME,
            1,
            ['nullable' => true],
            BannerInterface::ACTIVE_TO
        )->addColumn(
            BannerInterface::PLACEHOLDER_IDS,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            BannerInterface::PLACEHOLDER_IDS
        )->addColumn(
            BannerInterface::SORT_ORDER,
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false, 'default' => 0],
            BannerInterface::SORT_ORDER
        )->addColumn(
            BannerInterface::CONTENT,
            Table::TYPE_TEXT,
            16777217,
            ['nullable' => true],
            BannerInterface::CONTENT
        )->addColumn(
            BannerInterface::URL,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            BannerInterface::URL
        )->addColumn(
            BannerInterface::CONDITIONS_SERIALIZED,
            Table::TYPE_TEXT,
            16777217,
            ['nullable' => true],
            BannerInterface::CONDITIONS_SERIALIZED
        )->addColumn(
            BannerInterface::CUSTOMER_GROUP_IDS,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            BannerInterface::CUSTOMER_GROUP_IDS
        )->addColumn(
            BannerInterface::STORE_IDS,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            BannerInterface::STORE_IDS
        );
        $setup->getConnection()->dropTable($setup->getTable(BannerInterface::TABLE_NAME));
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable(PlaceholderInterface::TABLE_NAME)
        )->addColumn(
            PlaceholderInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'identity' => true, 'primary' => true],
            PlaceholderInterface::ID
        )->addColumn(
            PlaceholderInterface::NAME,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            PlaceholderInterface::NAME
        )->addColumn(
            PlaceholderInterface::RENDERER,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            PlaceholderInterface::RENDERER
        )->addColumn(
            PlaceholderInterface::IS_ACTIVE,
            Table::TYPE_INTEGER,
            1,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            PlaceholderInterface::IS_ACTIVE
        )->addColumn(
            PlaceholderInterface::LAYOUT_UPDATE_ID,
            Table::TYPE_INTEGER,
            11,
            ['unsigned' => false, 'nullable' => true],
            PlaceholderInterface::LAYOUT_UPDATE_ID
        )->addColumn(
            PlaceholderInterface::LAYOUT_POSITION,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            PlaceholderInterface::LAYOUT_POSITION
        )->addColumn(
            PlaceholderInterface::CONDITIONS_SERIALIZED,
            Table::TYPE_TEXT,
            16777217,
            ['nullable' => true],
            PlaceholderInterface::CONDITIONS_SERIALIZED
        )->addColumn(
            PlaceholderInterface::CSS,
            Table::TYPE_TEXT,
            16777217,
            ['nullable' => true],
            PlaceholderInterface::CSS
        )->addColumn(
            PlaceholderInterface::IDENTIFIER,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            PlaceholderInterface::IDENTIFIER
        );
        $setup->getConnection()->dropTable($setup->getTable(PlaceholderInterface::TABLE_NAME));
        $installer->getConnection()->createTable($table);

        $table = $setup->getConnection()->newTable(
            $setup->getTable(AnalyticsInterface::TABLE_NAME)
        )->addColumn(
            AnalyticsInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            AnalyticsInterface::ID
        )->addColumn(
            AnalyticsInterface::BANNER_ID,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            AnalyticsInterface::BANNER_ID
        )->addColumn(
            AnalyticsInterface::ACTION,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            AnalyticsInterface::ACTION
        )->addColumn(
            AnalyticsInterface::VALUE,
            Table::TYPE_DECIMAL,
            '12,1',
            ['nullable' => false],
            AnalyticsInterface::VALUE
        )->addColumn(
            AnalyticsInterface::REFERRER,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            AnalyticsInterface::REFERRER
        )->addColumn(
            AnalyticsInterface::REMOTE_ADDR,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            AnalyticsInterface::REMOTE_ADDR
        )->addColumn(
            AnalyticsInterface::SESSION_ID,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            AnalyticsInterface::SESSION_ID
        )->addColumn(
            AnalyticsInterface::CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            AnalyticsInterface::CREATED_AT
        );

        $setup->getConnection()->dropTable($setup->getTable(AnalyticsInterface::TABLE_NAME));
        $setup->getConnection()->createTable($table);
    }
}
