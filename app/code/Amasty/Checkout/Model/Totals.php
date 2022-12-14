<?php

namespace Amasty\Checkout\Model;

use Magento\Framework\DataObject;
use Amasty\Checkout\Api\Data\TotalsInterface;

class Totals extends DataObject implements TotalsInterface
{
    /**
     * @inheritdoc
     */
    public function getTotals()
    {
        return $this->getData(self::TOTALS);
    }

    /**
     * @inheritdoc
     */
    public function getShipping()
    {
        return $this->getData(self::SHIPPING);
    }

    /**
     * @inheritdoc
     */
    public function getPayment()
    {
        return $this->getData(self::PAYMENT);
    }
}
