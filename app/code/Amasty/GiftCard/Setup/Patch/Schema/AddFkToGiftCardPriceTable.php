<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Setup\Patch\Schema;

use Amasty\GiftCard\Api\Data\GiftCardPriceInterface;
use Amasty\GiftCard\Model\GiftCard\ResourceModel\GiftCardPrice;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddFkToGiftCardPriceTable implements SchemaPatchInterface
{
    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    public function __construct(
        ResourceInterface $moduleResource,
        SchemaSetupInterface $schemaSetup,
        MetadataPool $metadataPool
    ) {
        $this->moduleResource = $moduleResource;
        $this->schemaSetup = $schemaSetup;
        $this->metadataPool = $metadataPool;
    }

    public static function getDependencies(): array
    {
        return [UpdateTo200::class];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply()
    {
        $setupDataVersion = $this->moduleResource->getDataVersion('Amasty_GiftCard');
        if (!$setupDataVersion || version_compare($setupDataVersion, '2.0.0', '<')) {
            $this->addForeignKey($this->schemaSetup);
        }
    }

    private function addForeignKey(SchemaSetupInterface $setup)
    {
        $mainTable = $setup->getTable(GiftCardPrice::TABLE_NAME);
        $productTable = $setup->getTable('catalog_product_entity');

        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $entityField = $metadata->getLinkField();

        $setup->getConnection()->addForeignKey(
            $setup->getConnection()->getForeignKeyName(
                $mainTable,
                GiftCardPriceInterface::PRODUCT_ID,
                $productTable,
                $entityField
            ),
            $mainTable,
            GiftCardPriceInterface::PRODUCT_ID,
            $productTable,
            $entityField,
            Table::ACTION_CASCADE
        );
    }
}
