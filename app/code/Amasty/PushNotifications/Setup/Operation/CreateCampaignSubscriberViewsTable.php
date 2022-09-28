<?php
declare(strict_types=1);

namespace Amasty\PushNotifications\Setup\Operation;

use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Amasty\PushNotifications\Api\Data\SubscriberInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateCampaignSubscriberViewsTable
{
    const TABLE_NAME = 'amasty_notifications_campaign_subscriber_views';

    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->createTable(
            $this->createTable($setup)
        );
    }

    private function createTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(self::TABLE_NAME);
        $campaignTable = $setup->getTable(CreateCampaignTable::TABLE_NAME);
        $subscriberTable = $setup->getTable(CreateSubscriberTable::TABLE_NAME);

        return $setup->getConnection()
            ->newTable(
                $table
            )->setComment(
                'Amasty Push Notifications Campaign Subscriber Shown table'
            )->addColumn(
                'campaign_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'Campaign Id'
            )->addColumn(
                'subscriber_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'Subscriber Id'
            )->addColumn(
                'shown',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Shown'
            )->addForeignKey(
                $setup->getFkName(
                    $table,
                    'campaign_id',
                    $campaignTable,
                    CampaignInterface::CAMPAIGN_ID
                ),
                'campaign_id',
                $campaignTable,
                CampaignInterface::CAMPAIGN_ID,
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    $table,
                    'subscriber_id',
                    $subscriberTable,
                    SubscriberInterface::SUBSCRIBER_ID
                ),
                'subscriber_id',
                $subscriberTable,
                SubscriberInterface::SUBSCRIBER_ID,
                Table::ACTION_CASCADE
            );
    }
}
