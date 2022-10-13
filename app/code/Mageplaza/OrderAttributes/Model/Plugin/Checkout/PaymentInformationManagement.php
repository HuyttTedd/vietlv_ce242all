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

namespace Mageplaza\OrderAttributes\Model\Plugin\Checkout;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Mageplaza\OrderAttributes\Helper\Data;

/**
 * Class PaymentInformationManagement
 * @package Mageplaza\OrderAttributes\Model\Plugin\Checkout
 */
class PaymentInformationManagement
{
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var Data
     */
    protected $data;

    /**
     * ShippingInformationManagement constructor.
     *
     * @param CartRepositoryInterface $cartRepository
     * @param Data $data
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        Data $data
    ) {
        $this->cartRepository = $cartRepository;
        $this->data = $data;
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
        $quote = $this->cartRepository->get($cartId);

        $isEnable = $this->data->isEnabled($quote->getStoreId());
        $extensionAttributes = $paymentMethod->getExtensionAttributes();

        if ($isEnable && $extensionAttributes && $extensionAttributes->getMpOrderAttributes()) {
            $attributes = $extensionAttributes->getMpOrderAttributes();
            $quote->setMpOrderAttributes($attributes);
        }

        return [$cartId, $paymentMethod, $billingAddress];
    }
}
