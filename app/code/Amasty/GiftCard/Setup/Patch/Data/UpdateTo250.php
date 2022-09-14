<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Setup\Patch\Data;

use Amasty\GiftCard\Setup\Operation\UpdateDataTo250 as UpdateDataOperation;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;

class UpdateTo250 implements DataPatchInterface, NonTransactionableInterface
{
    /**
     * @var UpdateDataOperation
     */
    private $updateDataTo250;

    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    public function __construct(
        UpdateDataOperation $UpdateDataTo250,
        ResourceInterface $moduleResource,
        ModuleDataSetupInterface $setup
    ) {
        $this->updateDataTo250 = $UpdateDataTo250;
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

        if ($setupDataVersion && version_compare($setupDataVersion, '2.5.0', '<')) {
            $this->updateDataTo250->upgrade($this->setup);
        }
    }
}
