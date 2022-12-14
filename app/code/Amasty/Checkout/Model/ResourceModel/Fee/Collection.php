<?php

namespace Amasty\Checkout\Model\ResourceModel\Fee;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(\Amasty\Checkout\Model\Fee::class, \Amasty\Checkout\Model\ResourceModel\Fee::class);
    }
}
