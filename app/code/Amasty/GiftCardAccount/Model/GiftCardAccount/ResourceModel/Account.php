<?php
declare(strict_types=1);

namespace Amasty\GiftCardAccount\Model\GiftCardAccount\ResourceModel;

use Amasty\GiftCard\Model\Code\ResourceModel\Code;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Account extends AbstractDb
{
    public const TABLE_NAME = 'amasty_giftcard_account';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, GiftCardAccountInterface::ACCOUNT_ID);
    }

    /**
     * @param array $accountsData
     * @param array $codesData
     * @return void
     * @throws \Exception
     */
    public function insertMultipleAccounts(array $accountsData, array $codesData): void
    {
        try {
            $this->beginTransaction();
            $connection = $this->getConnection();
            $connection->insertMultiple(
                $this->getMainTable(),
                $accountsData
            );
            $connection->insertOnDuplicate(
                $this->getTable(Code::TABLE_NAME),
                $codesData
            );
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack(); //rollback everything if error
            throw $e;
        }
    }
}
