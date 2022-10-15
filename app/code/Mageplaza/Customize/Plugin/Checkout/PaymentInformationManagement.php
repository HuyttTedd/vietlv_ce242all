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
 * @package     Mageplaza_OrderAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Customize\Plugin\Checkout;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;

/**
 * Class PaymentInformationManagement
 * @package Mageplaza\Customize\Model\Plugin\Checkout
 */
class PaymentInformationManagement
{
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;


    /**
     * ShippingInformationManagement constructor.
     *
     * @param CartRepositoryInterface $cartRepository
     * @param Data $data
     */
    public function __construct(
        CartRepositoryInterface $cartRepository
    ) {
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param \Magento\Checkout\Model\PaymentInformationManagement $subject
     * @param string $cartId
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function beforeSavePaymentInformation(
        \Magento\Checkout\Model\PaymentInformationManagement $subject,
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        $extensionAttributes = $paymentMethod->getExtensionAttributes();

        if ($extensionAttributes && $extensionAttributes->getMpCustomizeAttributes()) {
            $attributes = $extensionAttributes->getMpCustomizeAttributes();
        }

        return [$cartId, $paymentMethod, $billingAddress];
    }
}
