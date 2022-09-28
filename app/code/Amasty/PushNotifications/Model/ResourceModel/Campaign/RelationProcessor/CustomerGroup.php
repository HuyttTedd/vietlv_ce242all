<?php
declare(strict_types=1);

namespace Amasty\PushNotifications\Model\ResourceModel\Campaign\RelationProcessor;

use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Amasty\PushNotifications\Model\CampaignCustomerGroup as CampaignCustomerGroupModel;
use Amasty\PushNotifications\Model\OptionSource\Campaign\SegmentationSource;
use Amasty\PushNotifications\Model\ResourceModel\CampaignCustomerGroup;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface;

class CustomerGroup implements RelationInterface
{
    /**
     * @var ChangedRelationProcessor
     */
    private $changedRelationProcessor;

    public function __construct(
        ChangedRelationProcessor $changedRelationProcessor
    ) {
        $this->changedRelationProcessor = $changedRelationProcessor;
    }

    public function processRelation(AbstractModel $object)
    {
        switch ($object->getSegmentationSource()) {
            case SegmentationSource::CUSTOMER_SEGMENTS:
                $this->changedRelationProcessor->delete($object, CampaignCustomerGroup::TABLE_NAME);
                break;
            case SegmentationSource::CUSTOMER_GROUPS:
                $oldGroups = (array)$object->getOrigData(CampaignInterface::CUSTOMER_GROUPS);
                $customerGroups = array_diff($object->getCustomerGroups(), ['']);
                $newGroups = array_diff($customerGroups, $oldGroups);
                $removedGroups = array_diff($oldGroups, $customerGroups);
                $this->changedRelationProcessor->replaceRelation(
                    $object,
                    $newGroups,
                    $removedGroups,
                    CampaignCustomerGroup::TABLE_NAME,
                    CampaignCustomerGroupModel::GROUP_ID
                );
                break;
        }
    }
}
