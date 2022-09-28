<?php
declare(strict_types=1);

namespace Amasty\PushNotifications\Model\ResourceModel\Campaign\RelationProcessor;

use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Model\AbstractModel;

class ChangedRelationProcessor
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    public function delete(AbstractModel $object, string $tableName)
    {
        $tableName = $this->resourceConnection->getTableName($tableName);
        $connection = $this->resourceConnection->getConnection();
        $connection->delete($tableName, [CampaignInterface::CAMPAIGN_ID . ' = ?' => $object->getCampaignId()]);
    }

    public function replaceRelation(
        AbstractModel $object,
        array $newValues,
        array $removedValues,
        string $tableName,
        string $field
    ) {
        $tableName = $this->resourceConnection->getTableName($tableName);
        $connection = $this->resourceConnection->getConnection();
        $newInsertData = [];
        foreach ($newValues as $value) {
            $newInsertData[] = [
                CampaignInterface::CAMPAIGN_ID => $object->getCampaignId(),
                $field => $value
            ];
        }
        if ($newInsertData) {
            $connection->insertMultiple($tableName, $newInsertData);
        }
        if ($removedValues) {
            foreach ($removedValues as $value) {
                $where = CampaignInterface::CAMPAIGN_ID . ' = ' . $object->getCampaignId()
                    . ' AND ' . $field . ' = ' . $connection->quote($value);
                $connection->delete(
                    $tableName,
                    $where
                );
            }
        }
    }
}
