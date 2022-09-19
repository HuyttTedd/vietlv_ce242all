<?php

namespace Amasty\PushNotifications\Api\Data;

interface CampaignStoreInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';
    const STORE_ID = 'store_id';
    const CAMPAIGN_ID = 'campaign_id';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return \Amasty\PushNotifications\Model\CampaignStore
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return \Amasty\PushNotifications\Model\CampaignStore
     */
    public function setStoreId($storeId);

    /**
     * @return int
     */
    public function getCampaignId();

    /**
     * @param $campaignId
     *
     * @return \Amasty\PushNotifications\Model\CampaignStore
     */
    public function setCampaignId($campaignId);
}
