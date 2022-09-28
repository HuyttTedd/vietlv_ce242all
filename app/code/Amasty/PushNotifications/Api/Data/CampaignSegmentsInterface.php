<?php

namespace Amasty\PushNotifications\Api\Data;

interface CampaignSegmentsInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const CAMPAIGN_SEGMENT_ID = 'campaign_segment_id';
    const SEGMENT_ID = 'segment_id';
    const CAMPAIGN_ID = 'campaign_id';
    /**#@-*/

    /**
     * @return int
     */
    public function getCampaignSegmentId();

    /**
     * @param int $id
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignSegmentsInterface
     */
    public function setCampaignSegmentId($id);

    /**
     * @return int
     */
    public function getSegmentId();

    /**
     * @param int $id
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignSegmentsInterface
     */
    public function setSegmentId($id);

    /**
     * @return int
     */
    public function getCampaignId();

    /**
     * @param int $id
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignSegmentsInterface
     */
    public function setCampaignId($id);
}
