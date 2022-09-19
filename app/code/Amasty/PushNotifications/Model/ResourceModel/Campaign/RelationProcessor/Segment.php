<?php
declare(strict_types=1);

namespace Amasty\PushNotifications\Model\ResourceModel\Campaign\RelationProcessor;

use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Amasty\PushNotifications\Model\CampaignSegments as CampaignSegmentsModel;
use Amasty\PushNotifications\Model\OptionSource\Campaign\SegmentationSource;
use Amasty\PushNotifications\Model\ResourceModel\CampaignSegments;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface;

class Segment implements RelationInterface
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
            case SegmentationSource::CUSTOMER_GROUPS:
                $this->changedRelationProcessor->delete($object, CampaignSegments::TABLE_NAME);
                break;
            case SegmentationSource::CUSTOMER_SEGMENTS:
                $oldSegments = (array)$object->getOrigData(CampaignInterface::CUSTOMER_SEGMENTS);
                $segments = array_diff($object->getSegments(), ['']);
                $newSegments = array_diff($segments, $oldSegments);
                $removedSegments = array_diff($oldSegments, $segments);
                $this->changedRelationProcessor->replaceRelation(
                    $object,
                    $newSegments,
                    $removedSegments,
                    CampaignSegments::TABLE_NAME,
                    CampaignSegmentsModel::SEGMENT_ID
                );
                break;
        }
    }
}
