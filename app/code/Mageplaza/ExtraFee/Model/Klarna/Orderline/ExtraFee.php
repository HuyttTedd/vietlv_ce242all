<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ExtraFee
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ExtraFee\Model\Klarna\Orderline;

use Klarna\Core\Api\BuilderInterface;
use Klarna\Core\Model\Checkout\Orderline\AbstractLine;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Sales\Model\AbstractModel;

/**
 * Class ExtraFee
 * @package Mageplaza\ExtraFee\Model\Klarna\Orderline
 */
class ExtraFee extends AbstractLine
{
    /**
     * @param BuilderInterface $checkout
     *
     * @return AbstractLine|void
     */
    public function collect(BuilderInterface $checkout)
    {
        /** @var AbstractModel|Quote $object */
        $object  = $checkout->getObject();
        /** @var Address $address */
        $address = $this->getAddress($object);
        $totals  = $address->getTotals();

        if (!empty($totals)) {
            $mpExtraFees = [];
            foreach ($totals as $key => $total) {
                if (strpos($key, 'mp_extra_fee_rule') !== false) {
                    $mpExtraFees['mp_extra_fee'][] = $this->processExtraFeeFromTotals($total);

                }
            }

            $checkout->addData($mpExtraFees);
        }
    }

    /**
     * @param Total $total
     *
     * @return array
     */
    private function processExtraFeeFromTotals($total)
    {
        /** @var Total $total */
        $amount = $total->getValue();

        return [
            'unit_price'   => $this->helper->toApiFloat($amount),
            'tax_rate'     => 0,
            'total_amount' => $this->helper->toApiFloat($amount),
            'tax_amount'   => 0,
            'title'        => (string)$total->getTitle(),
            'reference'    => $total->getCode(),
            'amount'       => $amount
        ];
    }

    /**
     * @param Quote $object
     *
     * @return mixed
     */
    private function getAddress($object)
    {
        $address = $object->getShippingAddress();

        if ($address) {
            return $address;
        }

        return $object->getBillingAddress();
    }

    /**
     * Add order details to checkout request
     *
     * @param BuilderInterface $checkout
     *
     * @return $this
     */
    public function fetch(BuilderInterface $checkout)
    {
        if (!empty($mpExtraFees = $checkout->getMpExtraFee())) {
            foreach ($mpExtraFees as $mpExtraFee) {
                $checkout->addOrderLine(
                    [
                        'type'             => 'surcharge',
                        'reference'        => $mpExtraFee['reference'],
                        'name'             => $mpExtraFee['title'],
                        'quantity'         => 1,
                        'unit_price'       => $mpExtraFee['unit_price'] ? : 0,
                        'tax_rate'         => $mpExtraFee['tax_rate'] ? : 0,
                        'total_amount'     => $mpExtraFee['total_amount'] ? : 0,
                        'total_tax_amount' => $mpExtraFee['tax_amount'] ? : 0
                    ]
                );
            }
        }

        return $this;
    }
}
