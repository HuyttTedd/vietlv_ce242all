<?php

namespace Amasty\PushNotifications\Model\ResourceModel;

use Amasty\PushNotifications\Model\CampaignSegments as CampaignSegmentsModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CampaignSegments extends AbstractDb
{
    const TABLE_NAME = 'amasty_notifications_campaign_segments';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, CampaignSegmentsModel::CAMPAIGN_SEGMENT_ID);
    }
}
