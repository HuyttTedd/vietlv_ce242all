<?php

namespace Amasty\PushNotifications\Model;

use Magento\Framework\Model\AbstractModel;

class CampaignCustomerGroup extends AbstractModel
{
    const CAMPAIGN_GROUP_ID = 'campaign_group_id';
    const GROUP_ID = 'group_id';
    const CAMPAIGN_ID = 'campaign_id';

    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\PushNotifications\Model\ResourceModel\CampaignCustomerGroup::class);
        $this->setIdFieldName(self::CAMPAIGN_GROUP_ID);
    }

    public function getCampaignGroupId(): int
    {
        return (int)$this->_getData(self::CAMPAIGN_GROUP_ID);
    }

    public function setCampaignGroupId($id): CampaignCustomerGroup
    {
        return $this->setData(self::CAMPAIGN_GROUP_ID, (int)$id);
    }

    public function getGroupId(): int
    {
        return (int)$this->_getData(self::GROUP_ID);
    }

    public function setGroupId($id): CampaignCustomerGroup
    {
        return $this->setData(self::GROUP_ID, (int)$id);
    }

    public function getCampaignId(): int
    {
        return (int)$this->_getData(self::CAMPAIGN_ID);
    }

    public function setCampaignId($id): CampaignCustomerGroup
    {
        return $this->setData(self::CAMPAIGN_ID, (int)$id);
    }
}
