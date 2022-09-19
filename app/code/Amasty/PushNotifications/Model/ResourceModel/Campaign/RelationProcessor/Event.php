<?php
declare(strict_types=1);

namespace Amasty\PushNotifications\Model\ResourceModel\Campaign\RelationProcessor;

use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Amasty\PushNotifications\Model\CampaignEvent as CampaignEventModel;
use Amasty\PushNotifications\Model\OptionSource\Campaign\NotificationType;
use Amasty\PushNotifications\Model\ResourceModel\CampaignEvent;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface;

class Event implements RelationInterface
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
        switch ($object->getNotificationType()) {
            case NotificationType::CRON_TYPE:
                $this->changedRelationProcessor->delete($object, CampaignEvent::TABLE_NAME);
                break;
            case NotificationType::EVENT_TYPE:
                $oldEvents = (array)$object->getOrigData(CampaignInterface::EVENTS);
                $events = array_diff($object->getEvents(), ['']);
                $newEvents = array_diff($events, $oldEvents);
                $removedEvents = array_diff($oldEvents, $events);
                $this->changedRelationProcessor->replaceRelation(
                    $object,
                    $newEvents,
                    $removedEvents,
                    CampaignEvent::TABLE_NAME,
                    CampaignEventModel::EVENT_TYPE
                );
                break;
        }
    }
}
