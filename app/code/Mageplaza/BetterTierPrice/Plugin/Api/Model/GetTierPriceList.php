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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BetterTierPrice\Plugin\Api\Model;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\TierPrice;
use Magento\Catalog\Model\ProductSearchResults;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\BetterTierPrice\Api\Data\MpTierPriceInterface;
use Mageplaza\BetterTierPrice\Helper\Data;
use Mageplaza\BetterTierPrice\Model\Config\Source\ProductPriceOptions;

/**
 * Class GetTierPriceList
 * @package Mageplaza\BetterTierPrice\Plugin\Api\Model
 */
class GetTierPriceList
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * GetTierPriceList constructor.
     *
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param GroupRepositoryInterface $groupRepository
     * @param Data $helperData
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepositoryInterface,
        GroupRepositoryInterface $groupRepository,
        Data $helperData
    ) {
        $this->helperData                  = $helperData;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->groupRepository             = $groupRepository;
    }

    /**
     * @param ProductRepositoryInterface $repository
     * @param ProductSearchResults $result
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function afterGetList(
        ProductRepositoryInterface $repository,
        $result
    ) {
        if (!$this->helperData->isEnabled()) {
            return $result;
        }
        $items = $result->getItems();

        /** @var Product $product */
        foreach ($items as &$product) {
            if ($product->getTypeId() === 'simple') {
                $this->getMpProductTierPrice($product);
            }
        }
        $result->setItems($items);

        return $result;
    }

    /**
     * @param ProductRepositoryInterface $repository
     * @param Product $result
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function afterGet(
        ProductRepositoryInterface $repository,
        $result
    ) {
        if (!$this->helperData->isEnabled()) {
            return $result;
        }
        if ($result->getTypeId() === 'simple') {
            $this->getMpProductTierPrice($result);
        }

        return $result;
    }

    /**
     * @param Product $product
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function getMpProductTierPrice($product)
    {
        $tierList     = [];
        $price        = $product->getFinalPrice();
        $specificList = $product->getMpSpecificCustomer();

        if (!is_array($specificList)) {
            $specificList = Data::jsonDecode($specificList);
        }

        if (is_array($specificList)
            && count($specificList) > 0
            && $this->helperData->isSpecificCustomerEnabled()
            && $price
        ) {
            foreach ($specificList as $item) {
                $tierList[] = $this->getTierSpecificList($item, $price);
            }
        }

        $normalList = $product->getTierPrices();
        if (is_array($normalList) && count($normalList) > 0 && $price) {
            foreach ($normalList as $item) {
                $tierList[] = $this->getTierNormalList($item, $price);
            }
        }

        $product->getExtensionAttributes()->setMpTierPriceTitle($this->helperData->getTierPriceTitle());
        $product->getExtensionAttributes()->setMpTierPriceList($tierList);

        return $product;
    }

    /**
     * @param array $item
     * @param float $price
     *
     * @return array
     */
    public function getTierSpecificList($item, $price)
    {
        try {
            $customer = $this->customerRepositoryInterface->getById($item['customer_id']);
            $group    = $customer->getGroupId();
        } catch (Exception $e) {
            $group = 0;
        }
        switch ($item['value_type']) {
            case ProductPriceOptions::VALUE_FIXED:
                $amount       = $this->getAmount($price, 0, 0, $item['price']);
                $pricePerItem = $this->getPricePerIem($price, 0, 0, $item['price']);
                break;
            case ProductPriceOptions::VALUE_PERCENT:
                $amount       = $this->getAmount($price, $item['percentage_value'], 0, 0);
                $pricePerItem = $this->getPricePerIem($price, $item['percentage_value'], 0, 0);
                break;
            case ProductPriceOptions::DISCOUNT_FIXED:
                $amount       = $this->getAmount($price, 0, $item['mp_discount_fixed'], 0);
                $pricePerItem = $this->getPricePerIem($price, 0, $item['mp_discount_fixed'], 0);
                break;
            default:
                $amount       = 0;
                $pricePerItem = 0;
        }

        return [
            MpTierPriceInterface::SAVE_AMOUNT       => number_format($amount, 2),
            MpTierPriceInterface::CUSTOMER_GROUP_ID => $group,
            MpTierPriceInterface::CUSTOMER_ID       => $item['customer_id'],
            MpTierPriceInterface::PRICE_PER_ITEM    => number_format($pricePerItem, 2),
            MpTierPriceInterface::QTY               => $item['price_qty']
        ];
    }

    /**
     * @param TierPrice $item
     * @param float $price
     *
     * @return array
     * @throws LocalizedException
     */
    public function getTierNormalList($item, $price)
    {
        $amount       = $this->getAmount($price, 0, 0, $item->getValue());
        $pricePerItem = $item->getValue();

        try {
            $group = $this->groupRepository->getById($item->getCustomerGroupId())->getId();
        } catch (Exception $e) {
            $list  = $this->groupRepository->getList(new SearchCriteria());
            $group = [];
            foreach ($list->getItems() as $listGroup) {
                $group[] = $listGroup->getId();
            }
            $group = implode(',', $group);
        }

        return [
            MpTierPriceInterface::SAVE_AMOUNT       => number_format($amount, 2),
            MpTierPriceInterface::CUSTOMER_GROUP_ID => $group,
            MpTierPriceInterface::CUSTOMER_ID       => 0,
            MpTierPriceInterface::PRICE_PER_ITEM    => number_format($pricePerItem, 2),
            MpTierPriceInterface::QTY               => $item->getQty()
        ];
    }

    /**
     * @param float $price
     * @param float $percentageValue
     * @param float $discountFixed
     * @param float $value
     *
     * @return float|int
     */
    protected function getAmount($price, $percentageValue, $discountFixed, $value)
    {
        if (!empty($percentageValue)) {
            return $percentageValue;
        }
        if (!empty($discountFixed)) {
            return number_format(($price - $discountFixed) / $price, 2) * 100;
        }

        return number_format(($price - $value) / $price, 2) * 100;
    }

    /**
     * @param float $price
     * @param float $percentageValue
     * @param float $discountFixed
     * @param float $value
     *
     * @return float|int
     */
    protected function getPricePerIem($price, $percentageValue, $discountFixed, $value)
    {
        if (!empty($percentageValue)) {
            return number_format($price * (100 - $percentageValue) / 100, 2);
        }
        if (!empty($discountFixed)) {
            return number_format($price - $discountFixed, 2);
        }

        return $value;
    }
}
