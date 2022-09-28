<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Setup\Operation;

use Amasty\GiftCard\Api\Data\ImageBakingInfoInterface;
use Amasty\GiftCard\Api\Data\ImageElementsInterface;
use Amasty\GiftCard\Model\Image\ResourceModel\ImageElements;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpdateSchemaTo270
{
    public const BACKING_TABLE_NAME = 'amasty_giftcard_image_baking_info';

    public function execute(SchemaSetupInterface $setup)
    {
        $this->transformBackingTable($setup);
    }

    private function transformBackingTable(SchemaSetupInterface $setup): void
    {
        $oldTableName = $setup->getTable(self::BACKING_TABLE_NAME);
        $newTableName = $setup->getTable(ImageElements::TABLE_NAME);
        if (!$setup->tableExists($oldTableName)) {
            return;
        }

        // Declarative Schema creates 'amasty_giftcard_image_elements' table.
        // In case there is 'amasty_giftcard_image_baking_info' table
        // we need to drop 'amasty_giftcard_image_elements' for ability to rename.
        if ($setup->tableExists($newTableName)) {
            $setup->getConnection()->dropTable($newTableName);
        }

        $setup->getConnection()->renameTable(
            $oldTableName,
            $newTableName
        );
        $setup->getConnection()->dropColumn(
            $newTableName,
            ImageBakingInfoInterface::IS_ENABLED
        );
        $setup->getConnection()->dropColumn(
            $newTableName,
            ImageBakingInfoInterface::TEXT_COLOR
        );

        $setup->getConnection()->addColumn(
            $newTableName,
            ImageElementsInterface::HEIGHT,
            [
                'type' => Table::TYPE_INTEGER,
                'comment' => 'Element Height',
                'unsigned' => true,
                'nullable' => false,
                'default' => 0
            ]
        );
        $setup->getConnection()->addColumn(
            $newTableName,
            ImageElementsInterface::WIDTH,
            [
                'type' => Table::TYPE_INTEGER,
                'comment' => 'Element Width',
                'unsigned' => true,
                'nullable' => false,
                'default' => 0
            ]
        );
        $setup->getConnection()->addColumn(
            $newTableName,
            ImageElementsInterface::CUSTOM_CSS,
            [
                'type' => Table::TYPE_TEXT,
                'comment' => 'Element Custom Css',
                'nullable' => true
            ]
        );
        $setup->getConnection()->changeColumn(
            $newTableName,
            ImageBakingInfoInterface::INFO_ID,
            ImageElementsInterface::ELEMENT_ID,
            [
                'type' => Table::TYPE_INTEGER,
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                'comment' => 'Image Element ID'
            ]
        );
    }
}
