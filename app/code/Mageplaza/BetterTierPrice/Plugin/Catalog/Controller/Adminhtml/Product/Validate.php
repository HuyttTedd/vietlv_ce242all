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

namespace Mageplaza\BetterTierPrice\Plugin\Catalog\Controller\Adminhtml\Product;

use Magento\Catalog\Controller\Adminhtml\Product\Validate as CatalogValidate;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Mageplaza\BetterTierPrice\Helper\Data;
use Mageplaza\BetterTierPrice\Model\Config\Source\ProductPriceOptions;

/**
 * Class Validate
 * @package Mageplaza\BetterTierPrice\Plugin\Catalog\Controller\Adminhtml\Product
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

    public function __construct(
        JsonFactory $resultJsonFactory,
        Data $helper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper            = $helper;
    }

    /**
     * @param CatalogValidate $subject
     * @param $response
     *
     * @return Json
     */
    public function afterExecute(
        CatalogValidate $subject,
        $response
    ) {
        $productData = $subject->getRequest()->getPost('product', []);
        if (isset($productData['mp_specific_customer']) && $productData['mp_specific_customer']) {
            $mpSpecificCustomer = $productData['mp_specific_customer'];
            $dupTierPriceQty    = [];
            $dupTierWebsiteId   = [];
            $dupTierCustomerId  = [];

            if ($mpSpecificCustomer && is_array($mpSpecificCustomer)) {
                foreach ($mpSpecificCustomer as $item) {
                    $dupTierPriceQty[]   = $item['price_qty'];
                    $dupTierWebsiteId[]  = $item['website_id'];
                    $dupTierCustomerId[] = $item['customer_id'];
                    if (empty($item['price_qty'])) {
                        $response = $this->helper->showError('Required fields cannot empty');

                        return $this->resultJsonFactory->create()->setData($response);
                    }

                    if ($item['value_type'] === ProductPriceOptions::DISCOUNT_FIXED && (!$item['mp_discount_fixed']
                            || $item['mp_discount_fixed'] <= 0)) {
                        $response = $this->helper->showError('Discount Amount price must be a number greater than 0.');

                        return $this->resultJsonFactory->create()->setData($response);
                    }

                    if ($item['value_type'] === ProductPriceOptions::VALUE_PERCENT && (!$item['percentage_value']
                            || $item['percentage_value'] <= 0)) {
                        $response = $this->helper->showError('Discount Percent price must be a number greater than 0.');

                        return $this->resultJsonFactory->create()->setData($response);
                    }
                }
            }

            if ($this->helper->hasDupes($dupTierPriceQty)
                && $this->helper->hasDupes($dupTierWebsiteId)
                && $this->helper->hasDupes($dupTierCustomerId)
            ) {
                $response = $this->helper->showError(
                    'We found a duplicate website, tier price for special customer(s) and quantity.'
                );

                return $this->resultJsonFactory->create()->setData($response);
            }
        }

        return $response;
    }
}
