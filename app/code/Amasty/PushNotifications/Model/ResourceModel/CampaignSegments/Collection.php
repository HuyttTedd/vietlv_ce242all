<?php

namespace Amasty\PushNotifications\Model\ResourceModel\CampaignSegments;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            \Amasty\PushNotifications\Model\CampaignSegments::class,
            \Amasty\PushNotifications\Model\ResourceModel\CampaignSegments::class
        );
    }
}
