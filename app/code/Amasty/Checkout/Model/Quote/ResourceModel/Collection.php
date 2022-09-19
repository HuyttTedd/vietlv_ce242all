<?php

namespace Amasty\Checkout\Model\Quote\ResourceModel;

/**
 * Class Collection
 */
class Collection extends \Magento\Quote\Model\ResourceModel\Quote\Collection
{
    /**
     * @return int|string
     */
    public function getSize()
    {
        return $this->getConnection()->fetchOne($this->getSelectCountSql(), $this->_bindParams);
    }
}
