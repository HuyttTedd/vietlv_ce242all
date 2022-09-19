<?php

namespace Amasty\PushNotifications\Setup\Operation;

use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Amasty\PushNotifications\Model\CampaignStore;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateCampaignStoreTable
{
    const TABLE_NAME = 'amasty_notifications_campaign_store';

    /**
     * @param SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->createTable(
            $this->createCampaignStoreTable($setup)
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @return Table
     *
     * @throws \Zend_Db_Exception
     */
    public function createCampaignStoreTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(self::TABLE_NAME);

        return $setup->getConnection()
            ->newTable(
                $table
            )->addColumn(
                CampaignStore::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Campaign store ID'
            )->addColumn(
                CampaignStore::CAMPAIGN_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false,],
                'Campaign Id'
            )->addColumn(
                CampaignStore::STORE_ID,
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )->addIndex(
                $setup->getIdxName(self::TABLE_NAME, ['campaign_id']),
                ['campaign_id']
            )->addForeignKey(
                $setup->getFkName(
                    self::TABLE_NAME,
                    CampaignStore::CAMPAIGN_ID,
                    CreateCampaignTable::TABLE_NAME,
                    CampaignInterface::CAMPAIGN_ID
                ),
                CampaignStore::CAMPAIGN_ID,
                $setup->getTable(CreateCampaignTable::TABLE_NAME),
                CampaignInterface::CAMPAIGN_ID,
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    self::TABLE_NAME,
                    CampaignStore::STORE_ID,
                    'store',
                    'store_id'
                ),
                CampaignStore::STORE_ID,
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->setComment(
                'Campaign Store'
            );
    }
}
