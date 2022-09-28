<?php

namespace Amasty\PushNotifications\Setup;

use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Operation\CreateCampaignCustomerGroupTable
     */
    private $createCampaignCustomerGroupTable;

    /**
     * @var Operation\CreateCampaignSegmentsTable
     */
    private $createCampaignSegmentsTable;

    /**
     * @var Operation\CreateCampaignSubscriberViewsTable
     */
    private $createCampaignSubscriberViewsTable;

    /**
     * @var Operation\AddNotificationTypeColumn
     */
    private $addNotificationTypeColumn;

    /**
     * @var Operation\CreateCampaignEventTable
     */
    private $createCampaignEventTable;

    public function __construct(
        Operation\CreateCampaignCustomerGroupTable $createCampaignCustomerGroupTable,
        Operation\CreateCampaignSegmentsTable $createCampaignSegmentsTable,
        Operation\CreateCampaignSubscriberViewsTable $createCampaignSubscriberViewsTable,
        Operation\AddNotificationTypeColumn $addNotificationTypeColumn,
        Operation\CreateCampaignEventTable $createCampaignEventTable
    ) {
        $this->createCampaignCustomerGroupTable = $createCampaignCustomerGroupTable;
        $this->createCampaignSegmentsTable = $createCampaignSegmentsTable;
        $this->createCampaignSubscriberViewsTable = $createCampaignSubscriberViewsTable;
        $this->addNotificationTypeColumn = $addNotificationTypeColumn;
        $this->createCampaignEventTable = $createCampaignEventTable;
    }

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (!$context->getVersion() || version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->createCampaignCustomerGroupTable->execute($setup);
            $this->createCampaignSegmentsTable->execute($setup);
            $this->addSegmentationColumn($setup);
        }

        if (!$context->getVersion() || version_compare($context->getVersion(), '1.2.4', '<')) {
            $this->createCampaignSubscriberViewsTable->execute($setup);
            $this->addShownUniqueColumn($setup);
        }

        if (!$context->getVersion() || version_compare($context->getVersion(), '1.3.1', '<')) {
            $this->addNotificationTypeColumn->execute($setup);
            $this->createCampaignEventTable->execute($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addSegmentationColumn($setup)
    {
        $column = [
            'type' => Table::TYPE_BOOLEAN,
            'nullable' => false,
            'comment' => 'Segmentation Source Column',
            'default' => 0
        ];

        $setup->getConnection()->addColumn(
            $setup->getTable(Operation\CreateCampaignTable::TABLE_NAME),
            CampaignInterface::SEGMENTATION_SOURCE,
            $column
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addShownUniqueColumn($setup)
    {
        $column = [
            'type' => Table::TYPE_INTEGER,
            'nullable' => false,
            'unsigned' => true,
            'comment' => 'Total Shown Unique Notifications',
            'default' => 0
        ];

        $setup->getConnection()->addColumn(
            $setup->getTable(Operation\CreateCampaignTable::TABLE_NAME),
            CampaignInterface::SHOWN_UNIQUE_COUNTER,
            $column
        );
    }
}
