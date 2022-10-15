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
 * @category  Mageplaza
 * @package   Mageplaza_BetterTierPrice
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BetterTierPrice\Plugin\Checkout\CustomerData;

use Magento\Checkout\CustomerData\Cart as CheckoutCart;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\BetterTierPrice\Helper\Data;

/**
 * Class Cart
 * @package Mageplaza\BetterTierPrice\Plugin\Checkout\CustomerData
 */
class Cart
{
    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * Cart constructor.
     *
     * @param Session $checkoutSession
     * @param Data $helperData
     */
    public function __construct(
        Session $checkoutSession,
        Data $helperData
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->helperData      = $helperData;
    }

    /**
     * @param CheckoutCart $cart
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function beforeGetSectionData(
        CheckoutCart $cart
    ) {
        if ($this->helperData->isEnabled()) {
            $this->checkoutSession->getQuote()->collectTotals();
        }
    }
}
