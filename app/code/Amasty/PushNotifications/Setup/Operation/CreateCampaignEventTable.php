<?php
declare(strict_types=1);

namespace Amasty\PushNotifications\Setup\Operation;

use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Amasty\PushNotifications\Model\CampaignEvent as CampaignEventModel;
use Amasty\PushNotifications\Model\ResourceModel\CampaignEvent;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateCampaignEventTable
{
    /**
     * @param SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->createTable(
            $this->createTable($setup)
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createTable($setup)
    {
        $mainTable = $setup->getTable(CampaignEvent::TABLE_NAME);
        $campaignTable = $setup->getTable(CreateCampaignTable::TABLE_NAME);

        return $setup->getConnection()
            ->newTable(
                $mainTable
            )->setComment(
                'Amasty Push Notifications Campaign Event Table'
            )->addColumn(
                CampaignEventModel::ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'ID'
            )->addColumn(
                CampaignEventModel::CAMPAIGN_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Campaign ID'
            )->addColumn(
                CampaignEventModel::EVENT_TYPE,
                Table::TYPE_TEXT,
                64,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Campaign Event Type'
            )->addIndex(
                $setup->getIdxName(CampaignEvent::TABLE_NAME, [CampaignEventModel::EVENT_TYPE]),
                [CampaignEventModel::EVENT_TYPE]
            )->addForeignKey(
                $setup->getFkName(
                    $mainTable,
                    CampaignEventModel::CAMPAIGN_ID,
                    $campaignTable,
                    CampaignInterface::CAMPAIGN_ID
                ),
                CampaignEventModel::CAMPAIGN_ID,
                $campaignTable,
                CampaignInterface::CAMPAIGN_ID,
                Table::ACTION_CASCADE
            );
    }
}
