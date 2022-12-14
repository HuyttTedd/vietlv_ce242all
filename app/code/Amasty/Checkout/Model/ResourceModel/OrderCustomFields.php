<?php

namespace Amasty\Checkout\Model\ResourceModel;

use Amasty\Checkout\Api\Data\OrderCustomFieldsInterface;

/**
 * Class OrderCustomFields
 */
class OrderCustomFields extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'amasty_amcheckout_order_custom_fields';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, OrderCustomFieldsInterface::ID);
    }
}
