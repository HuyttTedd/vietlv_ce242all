<?php
namespace Amasty\Ogrid\Model\Column\Address;

use Magento\Framework\Data\Collection;

class Billing extends \Amasty\Ogrid\Model\Column
{
    protected $_alias_prefix = 'amasty_ogrid_billing_';

    protected function _getFieldCondition($mainTableAlias)
    {
        return parent::_getFieldCondition($mainTableAlias) . ' and ' . $this->getAlias() . '.address_type="billing"';
    }
}