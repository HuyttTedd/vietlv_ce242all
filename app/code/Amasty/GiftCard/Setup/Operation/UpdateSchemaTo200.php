<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/


namespace Amasty\GiftCard\Setup\Operation;

use Amasty\GiftCard\Api\Data\CodeInterface;
use Amasty\GiftCard\Api\Data\CodePoolInterface;
use Amasty\GiftCard\Api\Data\CodePoolRuleInterface;
use Amasty\GiftCard\Api\Data\GiftCardPriceInterface;
use Amasty\GiftCard\Api\Data\ImageElementsInterface;
use Amasty\GiftCard\Api\Data\ImageInterface;
use Amasty\GiftCard\Model\CodePool\ResourceModel\CodePool;
use Amasty\GiftCard\Model\CodePool\ResourceModel\CodePoolRule;
use Amasty\GiftCard\Model\GiftCard\ResourceModel\GiftCardPrice;
use Amasty\GiftCard\Model\Image\ResourceModel\Image;
use Amasty\GiftCard\Model\Image\ResourceModel\ImageElements;
use Amasty\GiftCard\Model\OptionSource\Status;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Model\GiftCardAccount\ResourceModel\Account;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpdateSchemaTo200
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(
        ProductMetadataInterface $productMetadata
    ) {
        $this->productMetadata = $productMetadata;
    }

    public function execute(SchemaSetupInterface $setup)
    {
        $this->updateImageTable($setup);
        $this->updateCodeAndCodeSetTables($setup);
        $this->updatePriceTable($setup);
    }

    protected function updateImageTable(SchemaSetupInterface $setup)
    {
        $oldTableName = $setup->getTable('amasty_amgiftcard_image');
        $newTableName = $setup->getTable(Image::TABLE_NAME);
        $imageElementsTable = $setup->getTable(ImageElements::TABLE_NAME);

        if (!$setup->tableExists($oldTableName)) {
            return;
        }

         // Declarative schema creates 'amasty_giftcard_image' table.
         // We need to drop 'amasty_giftcard_image' table for ability to rename old table 'amasty_amgiftcard_image'
        if ($setup->tableExists($newTableName)
            && $setup->tableExists($imageElementsTable)
        ) {
            foreach ($setup->getConnection()->getForeignKeys($imageElementsTable) as $foreignKey) {
                $setup->getConnection()->dropForeignKey(
                    $imageElementsTable,
                    $foreignKey['FK_NAME']
                );
            }
            $setup->getConnection()->dropTable($newTableName);

            $setup->getConnection()->renameTable(
                $oldTableName,
                $newTableName
            );

            $setup->getConnection()->addForeignKey(
                $setup->getConnection()->getForeignKeyName(
                    $imageElementsTable,
                    ImageElementsInterface::IMAGE_ID,
                    $newTableName,
                    ImageInterface::IMAGE_ID
                ),
                $imageElementsTable,
                ImageElementsInterface::IMAGE_ID,
                $newTableName,
                ImageInterface::IMAGE_ID,
                Table::ACTION_CASCADE
            );
        } else {
            $setup->getConnection()->renameTable(
                $oldTableName,
                $newTableName
            );
        }

        $setup->getConnection()->changeColumn(
            $newTableName,
            'active',
            ImageInterface::STATUS,
            [
                'type' => Table::TYPE_SMALLINT,
                'comment' => 'Image Status',
                'nullable' => false,
                'default' => 0
            ]
        );
        $setup->getConnection()->modifyColumn(
            $newTableName,
            'code_pos_x',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Image Code X Position'
            ]
        );
        $setup->getConnection()->modifyColumn(
            $newTableName,
            'code_pos_y',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Image Code Y Position'
            ]
        );
        $setup->getConnection()->addColumn(
            $newTableName,
            'code_text_color',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 20,
                'comment' => 'Image Code Color',
                'nullable' => true
            ]
        );
        $setup->getConnection()->addColumn(
            $newTableName,
            ImageInterface::IS_USER_UPLOAD,
            [
                'type' => Table::TYPE_BOOLEAN,
                'length' => 1,
                'comment' => 'Is Image Uploaded by User',
                'nullable' => false,
                'default' => 0
            ]
        );
        $setup->getConnection()->addColumn(
            $newTableName,
            ImageInterface::GCARD_TITLE,
            [
                'type' => Table::TYPE_TEXT,
                'comment' => 'Gift Card Title',
                'nullable' => true
            ]
        );
        $setup->getConnection()->addColumn(
            $newTableName,
            ImageInterface::WIDTH,
            [
                'type' => Table::TYPE_INTEGER,
                'comment' => 'Gift Card Image Width',
                'unsigned' => true,
                'nullable' => false,
                'default' => ImageInterface::DEFAULT_WIDTH
            ]
        );
        $setup->getConnection()->addColumn(
            $newTableName,
            ImageInterface::HEIGHT,
            [
                'type' => Table::TYPE_INTEGER,
                'comment' => 'Gift Card Image Height',
                'unsigned' => true,
                'nullable' => false,
                'default' => ImageInterface::DEFAULT_HEIGHT
            ]
        );
    }

    public function updateCodeAndCodeSetTables(SchemaSetupInterface $setup)
    {
        $oldCodeSetTable = $setup->getTable('amasty_amgiftcard_code_set');
        $newCodeSetTable = $setup->getTable(CodePool::TABLE_NAME);
        $codePoolRuleTable = $setup->getTable(CodePoolRule::TABLE_NAME);
        $oldCodeTable = $setup->getTable('amasty_amgiftcard_code');
        $newCodeTable = $setup->getTable(\Amasty\GiftCard\Model\Code\ResourceModel\Code::TABLE_NAME);

        if (!$setup->tableExists($oldCodeSetTable) || !$setup->tableExists($oldCodeTable)) {
            return;
        }
        //changing code set table
        foreach ($setup->getConnection()->getForeignKeys($oldCodeTable) as $foreignKey) {
            $setup->getConnection()->dropForeignKey(
                $oldCodeTable,
                $foreignKey['FK_NAME']
            );
        }
        $setup->getConnection()->dropIndex(
            $oldCodeTable,
            $setup->getIdxName(
                $oldCodeTable,
                ['code'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            )
        );
        $setup->getConnection()->dropIndex(
            $oldCodeTable,
            $setup->getIdxName(
                $oldCodeTable,
                ['code_set_id']
            )
        );

        // Declarative schema creates 'amasty_giftcard_code_pool_rule' and 'amasty_giftcard_code_pool' table.
        // We need to drop 'amasty_giftcard_code_pool_rule' and 'amasty_giftcard_code_pool' tables
        // for ability to manipulate with old tables
        if ($setup->tableExists($codePoolRuleTable)) {
            $setup->getConnection()->dropTable($codePoolRuleTable);
        }
        if ($setup->tableExists($newCodeSetTable)) {
            $setup->getConnection()->dropTable($newCodeSetTable);
        }

        $setup->getConnection()->renameTable(
            $oldCodeSetTable,
            $newCodeSetTable
        );
        $setup->getConnection()->changeColumn(
            $newCodeSetTable,
            'code_set_id',
            CodePoolInterface::CODE_POOL_ID,
            [
                'type' => Table::TYPE_INTEGER,
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                'comment' => 'Code Pool ID'
            ]
        );
        $setup->getConnection()->dropColumn(
            $newCodeSetTable,
            'enabled'
        );

        //create copy of cod set table and rename it to code_pool_rule to save conditions
        $setup->getConnection()->createTable(
            $setup->getConnection()->createTableByDdl($newCodeSetTable, $codePoolRuleTable)->setComment(
                'Amasty GiftCard Code Pool Table'
            )
        );
        $query = $setup->getConnection()->insertFromSelect(
            $setup->getConnection()->select()->from($newCodeSetTable),
            $codePoolRuleTable
        );
        $setup->getConnection()->query($query);

        $setup->getConnection()->dropColumn(
            $codePoolRuleTable,
            CodePoolInterface::TITLE
        );
        $setup->getConnection()->dropColumn(
            $codePoolRuleTable,
            CodePoolInterface::TEMPLATE
        );
        $setup->getConnection()->changeColumn(
            $codePoolRuleTable,
            CodePoolInterface::CODE_POOL_ID,
            CodePoolRuleInterface::CODE_POOL_ID,
            [
                'type' => Table::TYPE_INTEGER,
                'comment' => 'Code Pool ID',
                'primary' => false,
                'nullable' => false,
                'unsigned' => true
            ]
        );
        $setup->getConnection()->dropIndex(
            $codePoolRuleTable,
            'PRIMARY'
        );
        $setup->getConnection()->addColumn(
            $codePoolRuleTable,
            CodePoolRuleInterface::RULE_ID,
            [
                'type' => Table::TYPE_INTEGER,
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                'comment' => 'Code Pool Rule ID'
            ]
        );
        $setup->getConnection()->addForeignKey(
            $setup->getConnection()->getForeignKeyName(
                $codePoolRuleTable,
                CodePoolRuleInterface::CODE_POOL_ID,
                $newCodeSetTable,
                CodePoolInterface::CODE_POOL_ID
            ),
            $codePoolRuleTable,
            CodePoolRuleInterface::CODE_POOL_ID,
            $newCodeSetTable,
            CodePoolInterface::CODE_POOL_ID,
            Table::ACTION_CASCADE
        );
        $setup->getConnection()->dropColumn(
            $newCodeSetTable,
            'conditions_serialized'
        );

        // Changing code table.
        // Declarative schema creates 'amasty_giftcard_code' table.
        // We need to drop 'amasty_giftcard_code' table for ability to rename old table 'amasty_amgiftcard_code'
        $cardAccountTable = $setup->getTable(Account::TABLE_NAME);
        $cardAccountTableFkName = $setup->getConnection()->getForeignKeyName(
            $cardAccountTable,
            GiftCardAccountInterface::CODE_ID,
            $newCodeTable,
            CodeInterface::CODE_ID
        );
        $setup->getConnection()->dropForeignKey($cardAccountTable, $cardAccountTableFkName);

        $oldCardAccountTable = $setup->getTable('amasty_amgiftcard_account');
        foreach ($setup->getConnection()->getForeignKeys($oldCardAccountTable) as $foreignKey) {
            $setup->getConnection()->dropForeignKey(
                $oldCardAccountTable,
                $foreignKey['FK_NAME']
            );
        }

        $setup->getConnection()->dropTable($newCodeTable);

        $setup->getConnection()->renameTable(
            $oldCodeTable,
            $newCodeTable
        );
        $setup->getConnection()->changeColumn(
            $newCodeTable,
            'code_set_id',
            CodeInterface::CODE_POOL_ID,
            [
                'type' => Table::TYPE_INTEGER,
                'comment' => 'Code Pool ID',
                'unsigned' => true,
                'nullable' => false
            ]
        );
        $setup->getConnection()->changeColumn(
            $newCodeTable,
            'used',
            CodeInterface::STATUS,
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => Status::AVAILABLE,
                'comment' => 'Code Status',

            ]
        );
        $setup->getConnection()->dropColumn(
            $newCodeTable,
            'enabled'
        );
    }

    protected function updatePriceTable(SchemaSetupInterface $setup)
    {
        $oldTableName = $setup->getTable('amasty_amgiftcard_price');
        $newTableName = $setup->getTable(GiftCardPrice::TABLE_NAME);

        $catalogProductEntityTable = $setup->getTable('catalog_product_entity');
        $storeWebsiteTable = $setup->getTable('store_website');
        $eavAttributeTable = $setup->getTable('eav_attribute');

        if (!$setup->tableExists($oldTableName)) {
            return;
        }

        // Declarative schema creates 'amasty_giftcard_price' table.
        // We need to drop 'amasty_giftcard_price' table for ability to rename old table 'amasty_amgiftcard_price'
        if ($setup->tableExists($newTableName)) {
            $setup->getConnection()->dropTable($newTableName);
        }
        // Drop old foreign keys and indexes
        foreach ($setup->getConnection()->getForeignKeys($oldTableName) as $foreignKey) {
            $setup->getConnection()->dropForeignKey(
                $oldTableName,
                $foreignKey['FK_NAME']
            );
        }
        $setup->getConnection()->dropIndex(
            $oldTableName,
            $setup->getIdxName(
                $oldTableName,
                ['product_id']
            )
        );
        $setup->getConnection()->dropIndex(
            $oldTableName,
            $setup->getIdxName(
                $oldTableName,
                ['website_id']
            )
        );
        $setup->getConnection()->dropIndex(
            $oldTableName,
            $setup->getIdxName(
                $oldTableName,
                ['attribute_id']
            )
        );

        $setup->getConnection()->renameTable(
            $oldTableName,
            $newTableName
        );
        $setup->getConnection()->modifyColumn(
            $newTableName,
            GiftCardPriceInterface::VALUE,
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'comment' => 'Price Value',
                'unsigned' => true,
                'nullable' => false
            ]
        );
        // Add new foreign keys and indexes
        $setup->getConnection()->addIndex(
            $newTableName,
            $setup->getIdxName(
                $newTableName,
                ['product_id']
            ),
            ['product_id']
        );
        $setup->getConnection()->addIndex(
            $newTableName,
            $setup->getIdxName(
                $newTableName,
                ['website_id']
            ),
            ['website_id']
        );
        $setup->getConnection()->addIndex(
            $newTableName,
            $setup->getIdxName(
                $newTableName,
                ['attribute_id']
            ),
            ['attribute_id']
        );
        $setup->getConnection()->addForeignKey(
            $setup->getConnection()->getForeignKeyName(
                $newTableName,
                GiftCardPriceInterface::WEBSITE_ID,
                $storeWebsiteTable,
                'website_id'
            ),
            $newTableName,
            GiftCardPriceInterface::WEBSITE_ID,
            $storeWebsiteTable,
            'website_id',
            Table::ACTION_CASCADE
        );
        $setup->getConnection()->addForeignKey(
            $setup->getConnection()->getForeignKeyName(
                $newTableName,
                GiftCardPriceInterface::ATTRIBUTE_ID,
                $eavAttributeTable,
                'attribute_id'
            ),
            $newTableName,
            GiftCardPriceInterface::ATTRIBUTE_ID,
            $eavAttributeTable,
            'attribute_id',
            Table::ACTION_CASCADE
        );
    }
}
