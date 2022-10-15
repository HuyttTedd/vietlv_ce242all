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

namespace Mageplaza\BetterTierPrice\Plugin\Catalog\Controller\Adminhtml\Product\Action\Attribute;

use Exception;
use Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Validate as AttributeValidate;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Mageplaza\BetterTierPrice\Helper\Data;
use Mageplaza\BetterTierPrice\Model\Config\Source\ProductPriceOptions;

/**
 * Class Validate
 * @package Mageplaza\BetterTierPrice\Plugin\Catalog\Controller\Adminhtml\Product\Action\Attribute
 */
class Validate
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepositoryInterface;

    /**
     * Validate constructor.
     *
     * @param JsonFactory $resultJsonFactory
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param Data $helper
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        CustomerRepositoryInterface $customerRepositoryInterface,
        Data $helper
    ) {
        $this->resultJsonFactory           = $resultJsonFactory;
        $this->helper                      = $helper;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
    }

    /**
     * @param AttributeValidate $subject
     * @param null $response
     *
     * @return Json
     */
    public function afterExecute(AttributeValidate $subject, $response)
    {
        $product            = $subject->getRequest()->getParam('product');
        $mpTierPriceData    = $subject->getRequest()->getParam('mpTierPriceData');
        $mpSpecificCustomer = $subject->getRequest()->getParam('mpSpecificCustomer');
        $mpTierPriceData    = json_decode($mpTierPriceData, true);
        $mpSpecificCustomer = json_decode($mpSpecificCustomer, true);

        if (isset($product['tier_price']['customer_group_change_checkbox']) && $mpTierPriceData) {
            $response = $this->validateTierPrice($mpTierPriceData, true, $response);
        }

        if (isset($product['mp_specific_customer']['specific_customer_change_checkbox']) && $mpSpecificCustomer) {
            $response = $this->validateTierPrice($mpSpecificCustomer, false, $response);
        }

        return $response;
    }

    /**
     * @param array $tierPrice
     * @param bool $isMpTierPriceData
     * @param null $response
     *
     * @return Json|mixed
     */
    protected function validateTierPrice($tierPrice, $isMpTierPriceData, $response)
    {
        $dupTierPriceQty  = [];
        $dupTierWebsiteId = [];
        $dupTierGroupId   = [];
        foreach ($tierPrice as $item) {
            $dupTierPriceQty[]  = $item['price_qty'];
            $dupTierWebsiteId[] = $item['website_id'];
            if (array_key_exists('customer_id', $item)) {
                try {
                    $customer         = $this->customerRepositoryInterface->getById($item['customer_id']);
                    $dupTierGroupId[] = $customer->getGroupId();
                } catch (Exception $e) {
                    $dupTierGroupId[] = 0;
                }
            } else {
                $dupTierGroupId[] = $item['cust_group'];
            }

            if (empty($item['price_qty'])) {
                $response = $this->helper->showError('Required fields cannot empty');

                return $this->resultJsonFactory->create()->setJsonData($response->toJson());
            }

            if ($item['value_type'] === ProductPriceOptions::VALUE_PERCENT
                && (!isset($item['percentage_value']) || (isset($item['percentage_value'])
                        && $item['percentage_value'] <= 0))) {
                $response = $this->helper->showError('Discount Percent price must be a number greater than 0.');

                return $this->resultJsonFactory->create()->setJsonData($response->toJson());
            }

            if ($item['value_type'] === ProductPriceOptions::DISCOUNT_FIXED
                && (!isset($item['mp_discount_fixed']) || (isset($item['mp_discount_fixed'])
                        && $item['mp_discount_fixed'] <= 0))) {
                $response = $this->helper->showError('Discount Amount price must be a number greater than 0.');

                return $this->resultJsonFactory->create()->setJsonData($response->toJson());
            }
        }

        if ($this->helper->hasDupes($dupTierPriceQty)
            && $this->helper->hasDupes($dupTierWebsiteId)
            && $this->helper->hasDupes($dupTierGroupId)
        ) {
            $response = $this->helper->showError(
                __('We found a duplicate website, tier price for special customer(s) and quantity.')
            );

            if ($isMpTierPriceData) {
                $response = $this->helper->showError(
                    __('We found a duplicate website, tier price, customer group and quantity.')
                );
            }

            return $this->resultJsonFactory->create()->setJsonData($response->toJson());
        }

        return $response;
    }
}
