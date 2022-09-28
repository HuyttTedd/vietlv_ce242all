<?php
declare(strict_types=1);

namespace Amasty\PushNotifications\Setup\Operation;

use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddNotificationTypeColumn
{
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(CreateCampaignTable::TABLE_NAME),
            CampaignInterface::NOTIFICATION_TYPE,
            [
                'type' => Table::TYPE_TEXT,
                'size' => 20,
                'default' => null,
                'comment' => 'Notification Type',
                'nullable' => true
            ]
        );
    }
}
