<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Setup\Patch\Data;

use Amasty\GiftCard\Setup\Operation\UpdateDataTo260 as UpdateDataOperation;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;

class UpdateTo260 implements DataPatchInterface, NonTransactionableInterface
{
    /**
     * @var UpdateDataOperation
     */
    private $updateDataTo260;

    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    public function __construct(
        UpdateDataOperation $UpdateDataTo260,
        ResourceInterface $moduleResource,
        ModuleDataSetupInterface $setup
    ) {
        $this->updateDataTo260 = $UpdateDataTo260;
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

        if (!$setupDataVersion || version_compare($setupDataVersion, '2.6.0', '<')) {
            $this->updateDataTo260->upgrade($this->setup);
        }
    }
}
