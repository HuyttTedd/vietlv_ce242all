<?php
declare(strict_types=1);

namespace Amasty\GiftCardAccount\Setup\Operation;

use Amasty\GiftCard\Api\Data\CodeInterface;
use Amasty\GiftCard\Model\Code\ResourceModel\Code;
use Amasty\GiftCard\Model\Config\Source\Usage;
use Amasty\GiftCardAccount\Api\Data\CustomerCardInterface;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Model\CustomerCard\ResourceModel\CustomerCard;
use Amasty\GiftCardAccount\Model\GiftCardAccount\ResourceModel\Account;
use Amasty\GiftCardAccount\Model\GiftCardAccount\ResourceModel\AccountTransaction;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpdateOldSchema
{
    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $this->updateAccountsTable($setup);
        $this->updateCustomerCardTable($setup);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    protected function updateAccountsTable(ModuleDataSetupInterface $setup)
    {
        $oldAccountTable = $setup->getTable('amasty_amgiftcard_account');
        $newAccountTable = $setup->getTable(Account::TABLE_NAME);
        $websiteTable = $setup->getTable('store_website');

        if (!$setup->tableExists($oldAccountTable)) {
            return;
        }

        // Declarative schema creates 'amasty_giftcard_account' table.
        // We need to drop 'amasty_giftcard_account' table for ability to rename old table 'amasty_amgiftcard_account'
        $customerCardTable = $setup->getTable(CustomerCard::TABLE_NAME);
        $oldCustomerCardTable = $setup->getTable('amasty_amgiftcard_customer_card');
        $accountTransactionTable = $setup->getTable(AccountTransaction::TABLE_NAME);
        if ($setup->tableExists($newAccountTable)
            && $setup->tableExists($customerCardTable)
            && $setup->tableExists($accountTransactionTable)
            && $setup->tableExists($oldCustomerCardTable)
        ) {
            // Drop foreign keys in related tables
            $customerCardTableFkName = $setup->getConnection()->getForeignKeyName(
                $customerCardTable,
                CustomerCardInterface::ACCOUNT_ID,
                $newAccountTable,
                GiftCardAccountInterface::ACCOUNT_ID
            );
            $setup->getConnection()->dropForeignKey($customerCardTable, $customerCardTableFkName);

            foreach ($setup->getConnection()->getForeignKeys($oldCustomerCardTable) as $foreignKey) {
                $setup->getConnection()->dropForeignKey(
                    $oldCustomerCardTable,
                    $foreignKey['FK_NAME']
                );
            }

            $accountTransactionTableFkName = $setup->getConnection()->getForeignKeyName(
                $accountTransactionTable,
                'account_id',
                $newAccountTable,
                GiftCardAccountInterface::ACCOUNT_ID
            );
            $setup->getConnection()->dropForeignKey($accountTransactionTable, $accountTransactionTableFkName);

            $setup->getConnection()->dropTable($newAccountTable);
        }

        $setup->getConnection()->renameTable(
            $oldAccountTable,
            $newAccountTable
        );
        $setup->getConnection()->dropColumn(
            $newAccountTable,
            'product_id'
        );
        $setup->getConnection()->dropColumn(
            $newAccountTable,
            'image_path'
        );
        $setup->getConnection()->dropColumn(
            $newAccountTable,
            'sender_name'
        );
        $setup->getConnection()->dropColumn(
            $newAccountTable,
            'sender_email'
        );
        $setup->getConnection()->dropColumn(
            $newAccountTable,
            'sender_message'
        );
        $setup->getConnection()->dropColumn(
            $newAccountTable,
            'recipient_name'
        );
        $setup->getConnection()->changeColumn(
            $newAccountTable,
            'status_id',
            GiftCardAccountInterface::STATUS,
            [
                'type' => Table::TYPE_SMALLINT,
                'comment' => 'Status',
                'unsigned' => true,
                'nullable' => false
            ]
        );
        $setup->getConnection()->modifyColumn(
            $newAccountTable,
            GiftCardAccountInterface::IS_SENT,
            [
                'type' => Table::TYPE_BOOLEAN,
                'comment' => 'Is Email Sent',
                'nullable' => false,
                'default' => false
            ]
        );
        $setup->getConnection()->modifyColumn(
            $newAccountTable,
            GiftCardAccountInterface::WEBSITE_ID,
            [
                'type' => Table::TYPE_SMALLINT,
                'comment' => 'Website ID',
                'length' => 5,
                'unsigned' => true
            ]
        );
        $setup->getConnection()->addColumn(
            $newAccountTable,
            GiftCardAccountInterface::IS_REDEEMABLE,
            [
                'type' => Table::TYPE_SMALLINT,
                'comment' => 'Redeemable',
                'nullable' => true
            ]
        );
        $setup->getConnection()->addColumn(
            $newAccountTable,
            GiftCardAccountInterface::USAGE,
            [
                'type' => Table::TYPE_TEXT,
                'comment' => 'Usage',
                'length' => 15,
                'default' => Usage::MULTIPLE,
                'nullable' => false
            ]
        );
        $setup->getConnection()->addColumn(
            $newAccountTable,
            GiftCardAccountInterface::RECIPIENT_PHONE,
            [
                'type' => Table::TYPE_TEXT,
                'comment' => 'Recipient Phone',
                'nullable' => true
            ]
        );
        $setup->getConnection()->addForeignKey(
            $setup->getConnection()->getForeignKeyName(
                $newAccountTable,
                GiftCardAccountInterface::WEBSITE_ID,
                $websiteTable,
                'website_id'
            ),
            $newAccountTable,
            GiftCardAccountInterface::WEBSITE_ID,
            $websiteTable,
            'website_id',
            Table::ACTION_CASCADE
        );

        // Recover dropped foreign key in 'amasty_giftcard_account_transaction' table
        $setup->getConnection()->addForeignKey(
            $setup->getConnection()->getForeignKeyName(
                $accountTransactionTable,
                'account_id',
                $newAccountTable,
                GiftCardAccountInterface::ACCOUNT_ID
            ),
            $accountTransactionTable,
            'account_id',
            $newAccountTable,
            GiftCardAccountInterface::ACCOUNT_ID,
            Table::ACTION_CASCADE
        );

        $codeTable = $setup->getTable(Code::TABLE_NAME);
        $setup->getConnection()->addForeignKey(
            $setup->getConnection()->getForeignKeyName(
                $newAccountTable,
                GiftCardAccountInterface::CODE_ID,
                $codeTable,
                CodeInterface::CODE_ID
            ),
            $newAccountTable,
            GiftCardAccountInterface::CODE_ID,
            $codeTable,
            CodeInterface::CODE_ID,
            Table::ACTION_CASCADE
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    protected function updateCustomerCardTable(ModuleDataSetupInterface $setup)
    {
        $oldCardTable = $setup->getTable('amasty_amgiftcard_customer_card');
        $newCardTable = $setup->getTable(CustomerCard::TABLE_NAME);
        $accountTable = $setup->getTable(Account::TABLE_NAME);
        $customerTable = $setup->getTable('customer_entity');

        if (!$setup->tableExists($oldCardTable)) {
            return;
        }
        // Declarative schema creates 'amasty_giftcard_customer_card' table.
        // We need to drop 'amasty_giftcard_customer_card' table
        // for ability to rename old table 'amasty_amgiftcard_customer_card'
        if ($setup->tableExists($newCardTable)) {
            $setup->getConnection()->dropTable($newCardTable);
        }

        $setup->getConnection()->renameTable(
            $oldCardTable,
            $newCardTable
        );

        $setup->getConnection()->modifyColumn(
            $newCardTable,
            CustomerCardInterface::ACCOUNT_ID,
            [
                'type' => Table::TYPE_INTEGER,
                'comment' => 'Account ID',
                'unsigned' => true,
                'nullable' => false
            ]
        );

        // Recover foreign keys after modifying column
        $setup->getConnection()->addForeignKey(
            $setup->getConnection()->getForeignKeyName(
                $newCardTable,
                CustomerCardInterface::ACCOUNT_ID,
                $accountTable,
                GiftCardAccountInterface::ACCOUNT_ID
            ),
            $newCardTable,
            CustomerCardInterface::ACCOUNT_ID,
            $accountTable,
            GiftCardAccountInterface::ACCOUNT_ID,
            Table::ACTION_CASCADE
        );
        $setup->getConnection()->addForeignKey(
            $setup->getConnection()->getForeignKeyName(
                $newCardTable,
                CustomerCardInterface::CUSTOMER_ID,
                $customerTable,
                'entity_id'
            ),
            $newCardTable,
            CustomerCardInterface::CUSTOMER_ID,
            $customerTable,
            'entity_id',
            Table::ACTION_CASCADE
        );
    }
}
