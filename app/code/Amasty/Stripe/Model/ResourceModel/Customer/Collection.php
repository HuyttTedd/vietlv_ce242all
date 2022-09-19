<?php

namespace Amasty\Stripe\Model\ResourceModel\Customer;

use Amasty\Stripe\Model\Customer;
use Amasty\Stripe\Model\ResourceModel\Customer as CustomerResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(Customer::class, CustomerResource::class);
    }
}
