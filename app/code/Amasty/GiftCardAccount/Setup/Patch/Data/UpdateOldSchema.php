<?php
declare(strict_types=1);

namespace Amasty\GiftCardAccount\Setup\Patch\Data;

use Amasty\GiftCardAccount\Setup\Operation\UpdateOldSchema as UpdateTables;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;

class UpdateOldSchema implements DataPatchInterface, NonTransactionableInterface
{
    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    /**
     * @var UpdateTables
     */
    private $updateTables;

    public function __construct(
        ResourceInterface $moduleResource,
        ModuleDataSetupInterface $setup,
        UpdateTables $updateTables
    ) {
        $this->moduleResource = $moduleResource;
        $this->setup = $setup;
        $this->updateTables = $updateTables;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply()
    {
        $giftCardSetupVersion = $this->moduleResource->getDbVersion('Amasty_GiftCard');

        if ($giftCardSetupVersion
            && version_compare($giftCardSetupVersion, '2.0.0', '<')
            && $this->setup->tableExists($this->setup->getTable('amasty_amgiftcard_account'))
        ) {
            $this->updateTables->execute($this->setup);
        }
    }
}
