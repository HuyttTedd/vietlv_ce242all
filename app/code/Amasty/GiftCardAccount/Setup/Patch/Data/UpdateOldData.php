<?php
declare(strict_types=1);

namespace Amasty\GiftCardAccount\Setup\Patch\Data;

use Amasty\GiftCardAccount\Model\GiftCardAccount\ResourceModel\Account;
use Amasty\GiftCardAccount\Setup\Operation\UpdateOldData as UpdateData;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;

class UpdateOldData implements DataPatchInterface, NonTransactionableInterface
{
    /**
     * @var UpdateData
     */
    private $updateData;

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    public function __construct(
        UpdateData $updateData,
        ModuleDataSetupInterface $setup
    ) {
        $this->updateData = $updateData;
        $this->setup = $setup;
    }

    public static function getDependencies(): array
    {
        return [
            UpdateOldSchema::class
        ];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply()
    {
        $accountTable = $this->setup->getTable(Account::TABLE_NAME);

        if ($this->setup->getConnection()->tableColumnExists($accountTable, 'order_id')) {
            $this->updateData->execute($this->setup);
        }
    }
}
