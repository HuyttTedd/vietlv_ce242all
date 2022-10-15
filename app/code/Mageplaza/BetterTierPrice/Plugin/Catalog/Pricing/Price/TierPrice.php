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

namespace Mageplaza\BetterTierPrice\Plugin\Catalog\Pricing\Price;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\BetterTierPrice\Helper\Data;

/**
 * Class TierPrice
 * @package Mageplaza\BetterTierPrice\Plugin\Catalog\Pricing\Price
 */
class TierPrice
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * TierPrice constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param Data $helperData
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Data $helperData
    ) {
        $this->storeManager = $storeManager;
        $this->helperData   = $helperData;
    }

    /**
     * @param \Magento\Catalog\Pricing\Price\Tierprice $tierprice
     * @param $tierPriceList
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function afterGetTierPriceList(
        \Magento\Catalog\Pricing\Price\Tierprice $tierprice,
        $tierPriceList
    ) {
        $storeId = $this->storeManager->getStore()->getId();
        if ($this->helperData->isEnabled($storeId) && $this->helperData->isApplicableOnly($storeId)) {
            $tierPriceList = $this->prepareDataForApplicableOnly($tierPriceList, $tierprice->getProduct());
        }

        return $tierPriceList;
    }

    /**
     * @param array $data
     * @param Product|SaleableInterface $object
     *
     * @return mixed
     */
    private function prepareDataForApplicableOnly($data, $object)
    {
        usort($data, function ($tierA, $tierB) {
            return ($tierA['price_qty'] <= $tierB['price_qty']) ? -1 : 1;
        });
        $min        = null;
        $prevKey    = null;
        $currentQty = null;
        foreach ($data as $key => &$tierPrice) {
            if (!$this->helperData->isSpecificCustomerEnabled() && isset($tierPrice['customer_id'])) {
                unset($data[$key]);
            }
            if ($min === null) {
                $min        = $object->getTypeId() === 'bundle'
                    ? $tierPrice['percentage_value']
                    : $tierPrice['website_price'];
                $prevKey    = $key;
                $currentQty = $tierPrice['price_qty'];
                continue;
            }
            if ($object->getTypeId() === 'bundle') {
                if ($tierPrice['percentage_value'] > $min) {
                    $min = $tierPrice['percentage_value'];
                    if ($tierPrice['price_qty'] === $currentQty) {
                        unset($data[$prevKey]);
                    }
                } else {
                    unset($data[$key]);
                }
            } elseif ($tierPrice['website_price'] < $min) {
                $min = $tierPrice['website_price'];
                if ($tierPrice['price_qty'] === $currentQty) {
                    unset($data[$prevKey]);
                }
            } else {
                unset($data[$key]);
            }
            $prevKey    = $key;
            $currentQty = $tierPrice['price_qty'];
        }
        unset($tierPrice);

        return $data;
    }
}
