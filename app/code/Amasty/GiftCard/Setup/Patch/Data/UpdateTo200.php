<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Setup\Patch\Data;

use Amasty\GiftCard\Setup\Operation\UpdateDataTo200;
use Amasty\GiftCard\Setup\Operation\UpdateSchemaTo200;
use Amasty\GiftCard\Setup\SampleData\Installer;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;

class UpdateTo200 implements DataPatchInterface, NonTransactionableInterface
{
    /**
     * @var UpdateDataTo200
     */
    private $updateDataTo200;

    /**
     * @var UpdateSchemaTo200
     */
    private $updateSchemaTo200;

    /**
     * @var Installer
     */
    private $installer;

    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    public function __construct(
        UpdateDataTo200 $updateDataTo200,
        UpdateSchemaTo200 $updateSchemaTo200,
        Installer $installer,
        ResourceInterface $moduleResource,
        ModuleDataSetupInterface $setup
    ) {
        $this->updateDataTo200 = $updateDataTo200;
        $this->updateSchemaTo200 = $updateSchemaTo200;
        $this->installer = $installer;
        $this->moduleResource = $moduleResource;
        $this->setup = $setup;
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

            $this->updateDataTo200->upgrade($this->setup);
        }
    }
}
