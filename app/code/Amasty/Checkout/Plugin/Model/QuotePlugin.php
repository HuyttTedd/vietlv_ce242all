<?php

namespace Amasty\Checkout\Plugin\Model;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote;

class QuotePlugin
{
    /**
     * @var AddressInterface
     */
    private $address;

    /**
     * @param Quote $subject
     * @param AddressInterface|null $address
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @codingStandardsIgnoreStart
     */
    public function beforeSetShippingAddress(Quote $subject, AddressInterface $address = null)
    {
        $this->address = $address;
    }

    /**
     * @param Quote $subject
     * @param Quote $result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSetShippingAddress(Quote $subject, Quote $result)
    {
        if ($this->address
            && !$this->address->getRegionId()
            && $this->address->getId() == $subject->getShippingAddress()->getId()
        ) {
            $subject->getShippingAddress()->setRegionId(null);
        }
    }
}
