<?php

namespace Amasty\PushNotifications\Setup\Operation;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Amasty\PushNotifications\Api\Data\CampaignInterface;

class CreateCampaignTable
{
    const TABLE_NAME = 'amasty_notifications_campaign';

    /**
     * @param SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->createTable(
            $this->createCampaignTable($setup)
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @return Table
     *
     * @throws \Zend_Db_Exception
     */
    private function createCampaignTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(self::TABLE_NAME);

        return $setup->getConnection()
            ->newTable(
                $table
            )->setComment(
                'Amasty Push Notifications Campaign table'
            )->addColumn(
                CampaignInterface::CAMPAIGN_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true
                ],
                'Campaign Id'
            )->addColumn(
                CampaignInterface::SCHEDULED,
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => true,
                    'default' => null
                ],
                'Scheduled'
            )->addColumn(
                CampaignInterface::STORE_ID,
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true, 'nullable' => false, 'default' => '0'
                ],
                'Campaign Store'
            )->addColumn(
                CampaignInterface::NAME,
                Table::TYPE_TEXT,
                255,
                [
                    'default' => null, 'nullable' => true
                ],
                'Name'
            )->addColumn(
                CampaignInterface::IS_ACTIVE,
                Table::TYPE_BOOLEAN,
                null,
                [
                    'default' => 0, 'unsigned' => true, 'nullable' => false
                ],
                'Is Active'
            )->addColumn(
                CampaignInterface::STATUS,
                Table::TYPE_SMALLINT,
                null,
                [
                    'default' => 0, 'unsigned' => true, 'nullable' => false
                ],
                'Status'
            )->addColumn(
                CampaignInterface::LOGO_PATH,
                Table::TYPE_TEXT,
                255,
                [
                    'default' => null, 'nullable' => true
                ],
                'Logo Path'
            )->addColumn(
                CampaignInterface::IS_DEFAULT_LOGO,
                Table::TYPE_BOOLEAN,
                null,
                [
                    'default' => 0, 'unsigned' => true, 'nullable' => false
                ],
                'Is Default Logo'
            )->addColumn(
                CampaignInterface::MESSAGE_TITLE,
                Table::TYPE_TEXT,
                255,
                [
                    'default' => null, 'nullable' => true
                ],
                'Message Title'
            )->addColumn(
                CampaignInterface::MESSAGE_BODY,
                Table::TYPE_TEXT,
                null,
                [
                    'default' => null, 'nullable' => true
                ],
                'Message Body'
            )->addColumn(
                CampaignInterface::BUTTON_NOTIFICATION_ENABLE,
                Table::TYPE_BOOLEAN,
                null,
                [
                    'default' => 0, 'unsigned' => true, 'nullable' => false
                ],
                'Button Notification Enable'
            )->addColumn(
                CampaignInterface::BUTTON_NOTIFICATION_TEXT,
                Table::TYPE_TEXT,
                255,
                [
                    'default' => null, 'nullable' => true
                ],
                'Button Notification Text'
            )->addColumn(
                CampaignInterface::BUTTON_NOTIFICATION_URL,
                Table::TYPE_TEXT,
                255,
                [
                    'default' => null, 'nullable' => true
                ],
                'Button Notification URL'
            )->addColumn(
                CampaignInterface::UTM_PARAMS,
                Table::TYPE_TEXT,
                null,
                [
                    'default' => null, 'nullable' => true
                ],
                'UTM Parameters'
            )->addColumn(
                CampaignInterface::SENT_COUNTER,
                Table::TYPE_INTEGER,
                null,
                [
                    'default' => 0, 'nullable' => false
                ],
                'Total Sent Notifications'
            )->addColumn(
                CampaignInterface::SHOWN_COUNTER,
                Table::TYPE_INTEGER,
                null,
                [
                    'default' => 0, 'nullable' => false
                ],
                'Total Shown Notifications'
            )->addColumn(
                CampaignInterface::CLICKED_COUNTER,
                Table::TYPE_INTEGER,
                null,
                [
                    'default' => 0, 'nullable' => false
                ],
                'Total Clicked Notifications'
            )->addColumn(
                CampaignInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false, 'default' => Table::TIMESTAMP_INIT
                ],
                'Created At'
            )->addColumn(
                CampaignInterface::UPDATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE
                ],
                'Updated At'
            );
    }
}
