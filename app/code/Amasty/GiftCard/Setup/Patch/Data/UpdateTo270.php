<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Setup\Patch\Data;

use Amasty\GiftCard\Setup\Operation\UpdateDataTo270;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateTo270 implements DataPatchInterface
{
    /**
     * @var UpdateDataTo270
     */
    private $updateDataTo270;

    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    public function __construct(
        UpdateDataTo270 $updateDataTo270,
        ResourceInterface $moduleResource,
        ModuleDataSetupInterface $setup
    ) {
        $this->updateDataTo270 = $updateDataTo270;
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

        if ($setupDataVersion && version_compare($setupDataVersion, '2.7.0', '<')) {
            $this->updateDataTo270->upgrade($this->setup);
        }
    }
}
