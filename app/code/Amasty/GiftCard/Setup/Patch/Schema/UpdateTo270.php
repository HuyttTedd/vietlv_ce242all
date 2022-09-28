<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Setup\Patch\Schema;

use Amasty\GiftCard\Setup\Operation\UpdateSchemaTo270;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpdateTo270 implements SchemaPatchInterface
{
    /**
     * @var UpdateSchemaTo270
     */
    private $updateSchemaTo270;

    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    public function __construct(
        UpdateSchemaTo270 $updateSchemaTo270,
        ResourceInterface $moduleResource,
        SchemaSetupInterface $schemaSetup
    ) {
        $this->updateSchemaTo270 = $updateSchemaTo270;
        $this->moduleResource = $moduleResource;
        $this->schemaSetup = $schemaSetup;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): void
    {
        $setupDataVersion = $this->moduleResource->getDataVersion('Amasty_GiftCard');

        if ($setupDataVersion && version_compare($setupDataVersion, '2.7.0', '<')) {
            $this->updateSchemaTo270->execute($this->schemaSetup);
        }
    }
}
