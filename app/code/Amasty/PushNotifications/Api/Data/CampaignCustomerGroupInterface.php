<?php

namespace Amasty\PushNotifications\Api\Data;

interface CampaignCustomerGroupInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const CAMPAIGN_GROUP_ID = 'campaign_group_id';
    const GROUP_ID = 'group_id';
    const CAMPAIGN_ID = 'campaign_id';
    /**#@-*/

    /**
     * @return int
     */
    public function getCampaignGroupId();

    /**
     * @param int $id
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignCustomerGroupInterface
     */
    public function setCampaignGroupId($id);

    /**
     * @return int
     */
    public function getGroupId();

    /**
     * @param int $id
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignCustomerGroupInterface
     */
    public function setGroupId($id);

    /**
     * @return int
     */
    public function getCampaignId();

    /**
     * @param int $id
     *
     * @return \Amasty\PushNotifications\Api\Data\CampaignCustomerGroupInterface
     */
    public function setCampaignId($id);
}
