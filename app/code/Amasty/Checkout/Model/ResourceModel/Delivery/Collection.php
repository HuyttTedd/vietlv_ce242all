<?php

namespace Amasty\Checkout\Model\ResourceModel\Delivery;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(\Amasty\Checkout\Model\Delivery::class, \Amasty\Checkout\Model\ResourceModel\Delivery::class);
    }
}
