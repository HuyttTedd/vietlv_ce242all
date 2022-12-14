<?php

namespace Amasty\Checkout\Model\ResourceModel;

use Amasty\Checkout\Api\Data\QuotePasswordsInterface;

/**
 * Class QuotePasswords
 */
class QuotePasswords extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'amasty_amcheckout_quote_passwords';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, QuotePasswordsInterface::ENTITY_ID);
    }
}
