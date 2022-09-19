<?php

namespace Amasty\PushNotifications\Model;

use Magento\Framework\Model\AbstractModel;

class CampaignSegments extends AbstractModel
{
    const CAMPAIGN_SEGMENT_ID = 'campaign_segment_id';
    const SEGMENT_ID = 'segment_id';
    const CAMPAIGN_ID = 'campaign_id';

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\CampaignSegments::class);
        $this->setIdFieldName(self::CAMPAIGN_SEGMENT_ID);
    }

    public function getCampaignSegmentId(): int
    {
        return (int)$this->_getData(self::CAMPAIGN_SEGMENT_ID);
    }

    public function setCampaignSegmentId($id): CampaignSegments
    {
        return $this->setData(self::CAMPAIGN_SEGMENT_ID, (int)$id);
    }

    public function getSegmentId(): int
    {
        return (int)$this->_getData(self::SEGMENT_ID);
    }

    public function setSegmentId($id): CampaignSegments
    {
        return $this->setData(self::SEGMENT_ID, (int)$id);
    }

    public function getCampaignId(): int
    {
        return (int)$this->_getData(self::CAMPAIGN_ID);
    }

    public function setCampaignId($id): CampaignSegments
    {
        return $this->setData(self::CAMPAIGN_ID, (int)$id);
    }
}
