<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Setup\Patch\Schema;

use Amasty\GiftCard\Setup\Operation\UpdateSchemaTo200;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpdateTo200 implements SchemaPatchInterface
{
    /**
     * @var UpdateSchemaTo200
     */
    private $updateSchemaTo200;

    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    public function __construct(
        UpdateSchemaTo200 $updateSchemaTo200,
        ResourceInterface $moduleResource,
        SchemaSetupInterface $schemaSetup
    ) {
        $this->updateSchemaTo200 = $updateSchemaTo200;
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

        if ($setupDataVersion && version_compare($setupDataVersion, '2.0.0', '<')) {
            $disabled = explode(',', str_replace(' ', ',', ini_get('disable_functions')));
            if (!in_array('class_exists', $disabled)
                && function_exists('class_exists')
                && class_exists(\Amasty\GiftCard\Cron\SendGiftCard::class)) {
                throw new \RuntimeException("This update requires removing folder app/code/Amasty/GiftCard\n"
                    . "Remove this folder and unpack new version of package into app/code/Amasty/\n"
                    . "Run `php bin/magento setup:upgrade` again\n");
            }

            $this->updateSchemaTo200->execute($this->schemaSetup);
        }
    }
}
