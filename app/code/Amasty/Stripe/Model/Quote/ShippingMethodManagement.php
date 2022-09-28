<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stripe
 */

namespace Amasty\Stripe\Model\Quote;

use Amasty\Stripe\Api\Quote\ApplePayShippingMethodManagementInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Cart\ShippingMethodConverter;
use Magento\Quote\Model\Quote\TotalsCollector;
use Magento\Quote\Model\ShippingAssignment;
use Magento\Quote\Model\ShippingAssignmentFactory;
use Magento\Quote\Model\ShippingFactory;
use Magento\Quote\Model\ShippingMethodManagement as ShippingMethodManagementOriginal;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class ShippingMethodManagement extends ShippingMethodManagementOriginal implements
    ApplePayShippingMethodManagementInterface
{

    /**
     * @var Totals
     */
    private $totals;

    /**
     * @var AddressMerger
     */
    private $addressMerger;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var CartExtensionFactory
     */
    private $cartExtensionFactory;

    /**
     * @var ShippingAssignmentFactory
     */
    private $shippingAssignmentFactory;

    /**
     * @var ShippingFactory
     */
    private $shippingFactory;

    public function __construct(
        CartRepositoryInterface $quoteRepository,
        ShippingMethodConverter $converter,
        AddressRepositoryInterface $addressRepository,
        TotalsCollector $totalsCollector,
        Totals $totals,
        AddressMerger $addressMerger,
        PriceCurrencyInterface $priceCurrency,
        CartExtensionFactory $cartExtensionFactory,
        ShippingAssignmentFactory $shippingAssignmentFactory,
        ShippingFactory $shippingFactory
    ) {
        parent::__construct($quoteRepository, $converter, $addressRepository, $totalsCollector);
        $this->totals = $totals;
        $this->addressMerger = $addressMerger;
        $this->priceCurrency = $priceCurrency;
        $this->cartExtensionFactory = $cartExtensionFactory;
        $this->shippingAssignmentFactory = $shippingAssignmentFactory;
        $this->shippingFactory = $shippingFactory;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function set($cartId, $carrierCode, $methodCode, AddressInterface $address = null, $calculateTotals = true)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        if ($address) {
            $this->processShippingAssignment($quote, $address, $carrierCode, $methodCode);
            $this->addressMerger->merge($quote, $address);
        }

        try {
            $this->apply($cartId, $carrierCode, $methodCode);
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            throw $e;
        }

        if ($calculateTotals) {
            return $this->totals->getTotals($quote);
        }

        return [];
    }

    /**
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     *
     * @return array|\Magento\Quote\Api\Data\ShippingMethodInterface[]
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function estimateByExtendedAddress($cartId, AddressInterface $address)
    {
        $estimatesArray = [];
        $estimates = parent::estimateByExtendedAddress($cartId, $address);
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        if ($estimates) {
            if ($shippingMethodCode = $quote->getShippingAddress()->getShippingMethod()) {
                if ($chosenMethodKey = $this->getChosenMethodKey($estimates, $shippingMethodCode)) {
                    // set chosen shipping method to first for google/apple pay default option
                    array_unshift($estimates, $estimates[$chosenMethodKey]);
                    unset($estimates[$chosenMethodKey+1]);
                } else {
                    $this->set($cartId, $estimates[0]->getCarrierCode(), $estimates[0]->getMethodCode());
                }
            } else {
                $this->set($cartId, $estimates[0]->getCarrierCode(), $estimates[0]->getMethodCode());
            }
            $estimatesArray = $this->getEstimatesArray($quote, $estimates);
        }

        return [$estimatesArray, $this->totals->getTotals($quote)];
    }

    /**
     * @param CartInterface $quote
     * @param AddressInterface $shippingAddress
     * @param string $carrierCode
     * @param string $methodCode
     * @return CartInterface
     */
    public function processShippingAssignment(
        CartInterface $quote,
        AddressInterface $shippingAddress,
        string $carrierCode,
        string $methodCode
    ): CartInterface {
        $cartExtension = $quote->getExtensionAttributes();
        if ($cartExtension === null) {
            $cartExtension = $this->cartExtensionFactory->create();
        }

        $shippingAssignments = $cartExtension->getShippingAssignments();
        if (empty($shippingAssignments)) {
            /** @var ShippingAssignment $shippingAssignment */
            $shippingAssignment = $this->shippingAssignmentFactory->create();
        } else {
            $shippingAssignment = $shippingAssignments[0];
        }

        $shipping = $shippingAssignment->getShipping();
        if ($shipping === null) {
            $shipping = $this->shippingFactory->create();
        }

        $shippingAddress->setLimitCarrier($carrierCode);
        $method = $carrierCode . '_' . $methodCode;
        $shippingAddress->setShippingMethod($method);
        $shipping->setAddress($shippingAddress);
        $shipping->setMethod($method);
        $shippingAssignment->setShipping($shipping);
        $cartExtension->setShippingAssignments([$shippingAssignment]);
        $quote->setTotalsCollectedFlag(false);

        return $quote->setExtensionAttributes($cartExtension);
    }

    /**
     * @param array $estimates
     * @param string $choosenShippingCode
     * @return int|null
     */
    private function getChosenMethodKey(array $estimates, string $choosenShippingCode): ?int
    {
        foreach ($estimates as $key => $shippingMethod) {
            $estimatedMethodCode = $shippingMethod->getCarrierCode() . '_' . $shippingMethod->getMethodCode();
            if ($choosenShippingCode === $estimatedMethodCode) {
                return (int)$key;
            }
        }

        return null;
    }

    /**
     * @param CartInterface $quote
     * @param array $estimates
     * @return array
     */
    private function getEstimatesArray(CartInterface $quote, array $estimates): array
    {
        $estimatesArray = [];
        /** @var \Magento\Quote\Api\Data\ShippingMethodInterface $shippingMethod */
        foreach ($estimates as $shippingMethod) {
            $estimatesArray[] = [
                'carrier_code'  => $shippingMethod->getCarrierCode(),
                'method_code'   => $shippingMethod->getMethodCode(),
                'method_title'  => $shippingMethod->getMethodTitle(),
                'carrier_title' => $shippingMethod->getCarrierTitle(),
                'amount'        => $this->priceCurrency->round($shippingMethod->getAmount()),
            ];
        }

        return $estimatesArray;
    }
}
