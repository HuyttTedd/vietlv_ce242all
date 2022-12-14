<?php

namespace Amasty\Checkout\Model\ResourceModel;

use Amasty\Checkout\Api\Data\AdditionalFieldsInterface;

/**
 * Class AdditionalFields
 */
class AdditionalFields extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'amasty_amcheckout_additional';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, AdditionalFieldsInterface::ID);
    }
}
