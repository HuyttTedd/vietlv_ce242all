<?php
namespace Amasty\Checkout\Model\ResourceModel\Field;

/**
 * Class Store
 */
class Store extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'amasty_amcheckout_field_store';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, 'id');
    }
}
