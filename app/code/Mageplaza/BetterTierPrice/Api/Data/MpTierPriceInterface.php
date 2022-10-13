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
 * @package     Mageplaza_BetterTierPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BetterTierPrice\Api\Data;

/**
 * Interface MpTierPriceInterface
 * @package Mageplaza\BetterTierPrice\Api\Data
 */
interface MpTierPriceInterface
{
    const QTY               = 'qty';
    const SAVE_AMOUNT       = 'save_amount';
    const PRICE_PER_ITEM    = 'price_per_item';
    const CUSTOMER_ID       = 'customer_id';
    const CUSTOMER_GROUP_ID = 'customer_group_id';

    /**
     * @return int
     */
    public function getQty();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setQty($value);

    /**
     * @return int
     */
    public function getSaveAmount();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setSaveAmount($value);

    /**
     * @return string
     */
    public function getPricePerItem();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPricePerItem($value);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setCustomerId($value);

    /**
     * @return int
     */
    public function getCustomerGroupId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setCustomerGroupId($value);
}
