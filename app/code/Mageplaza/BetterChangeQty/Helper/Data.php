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
 * @package     Mageplaza_BetterChangeQty
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BetterChangeQty\Helper;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductTierPriceInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Data as TaxHelper;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\BetterChangeQty\Model\Config\Source\ApplyPage;
use Mageplaza\BetterChangeQty\Model\Config\Source\ChangeQtyStep;
use Mageplaza\BetterChangeQty\Model\Config\Source\MaxQtyStep;
use Mageplaza\Core\Helper\AbstractData;

/**
 * Class Data
 * @package Mageplaza\BetterChangeQty\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH      = 'mpbetterchangeqty';
    const DEFAULT_OPT_TMPL        = 'Buy {{qty}}';
    const DEFAULT_OPT_TMPL_MULTI  = 'Buy {{qty}} for {{price}} each';
    const DEFAULT_OPT_TMPL_TIER   = 'Buy {{qty}} for {{price}} each and save {{percent}}';
    const NOT_ALLOWED_IN_WISHLIST = ['downloadable'];
    const NOT_ALLOWED_IN_CATEGORY = ['downloadable', 'bundle', 'grouped', 'mpgiftcard'];

    /**
     * @var ApplyPage
     */
    protected $applyPage;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var FormatInterface
     */
    protected $format;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var GroupManagementInterface
     */
    protected $groupManagement;

    /**
     * @var TaxHelper
     */
    protected $taxHelper;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ApplyPage $applyPage
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param StockRegistryInterface $stockRegistry
     * @param PriceCurrencyInterface $priceCurrency
     * @param ProductRepositoryInterface $productRepository
     * @param TimezoneInterface $timezone
     * @param FormatInterface $format
     * @param Session $session
     * @param GroupManagementInterface $groupManagement
     * @param TaxHelper $taxHelper
     */
    public function __construct(
        Context $context,
        ApplyPage $applyPage,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        StockRegistryInterface $stockRegistry,
        PriceCurrencyInterface $priceCurrency,
        ProductRepositoryInterface $productRepository,
        TimezoneInterface $timezone,
        FormatInterface $format,
        Session $session,
        GroupManagementInterface $groupManagement,
        TaxHelper $taxHelper
    ) {
        $this->applyPage         = $applyPage;
        $this->stockRegistry     = $stockRegistry;
        $this->priceCurrency     = $priceCurrency;
        $this->productRepository = $productRepository;
        $this->timezone          = $timezone;
        $this->format            = $format;
        $this->session           = $session;
        $this->groupManagement   = $groupManagement;
        $this->taxHelper         = $taxHelper;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param null $scopeId
     *
     * @return int
     */
    public function getChangeQtyStep($scopeId = null)
    {
        return (int) $this->getConfigGeneral('change_qty_step', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return float
     */
    public function getQtyStepValue($scopeId = null)
    {
        return $this->getConfigGeneral('qty_step_value', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return array
     */
    public function getCustomStep($scopeId = null)
    {
        $steps = $this->getConfigGeneral('custom_step', $scopeId);

        return array_map('intval', explode(',', $steps));
    }

    /**
     * @param null $scopeId
     *
     * @return float
     */
    public function getMaxQtyStep($scopeId = null)
    {
        return $this->getConfigGeneral('max_qty_step', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return float
     */
    public function getLimitNumberOption($scopeId = null)
    {
        return (float) $this->getConfigGeneral('limit_number_option', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return float
     */
    public function getMaxQtyValue($scopeId = null)
    {
        return $this->getConfigGeneral('max_qty_value', $scopeId) ?: 1;
    }

    /**
     * @param null $scopeId
     *
     * @return bool
     */
    public function isAllowOpenQty($scopeId = null)
    {
        return (bool) $this->getConfigGeneral('allow_open_qty', $scopeId);
    }

    /**
     * @param null $scopeId
     *
     * @return string
     */
    public function getOptionTemplate($scopeId = null)
    {
        return $this->getConfigGeneral('opt_tmpl', $scopeId) ?: self::DEFAULT_OPT_TMPL;
    }

    /**
     * @param null $scopeId
     *
     * @return string
     */
    public function getOptionTemplateMultiQty($scopeId = null)
    {
        return $this->getConfigGeneral('opt_tmpl_multi_qty', $scopeId) ?: self::DEFAULT_OPT_TMPL_MULTI;
    }

    /**
     * @param null $scopeId
     *
     * @return string
     */
    public function getOptionTemplateTierPrice($scopeId = null)
    {
        return $this->getConfigGeneral('opt_tmpl_tier_price', $scopeId) ?: self::DEFAULT_OPT_TMPL_TIER;
    }

    /**
     * @param null $scopeId
     *
     * @return array|string
     */
    public function getApplyCategory($scopeId = null)
    {
        $categories = $this->getConfigGeneral('apply_category', $scopeId);

        return $categories ? explode(',', $categories) : [];
    }

    /**
     * @param null $scopeId
     *
     * @return array
     */
    public function getApplyProduct($scopeId = null)
    {
        $types = $this->getConfigGeneral('apply_product', $scopeId);

        return explode(',', $types);
    }

    /**
     * @param null $scopeId
     *
     * @return array
     */
    public function getApplyPage($scopeId = null)
    {
        $pages = $this->getConfigGeneral('apply_page', $scopeId);

        return explode(',', $pages);
    }

    /**
     * @param null $scopeId
     *
     * @return bool
     */
    public function isHideTierPriceNotice($scopeId = null)
    {
        return (bool) $this->getConfigGeneral('hide_tier_price_notice', $scopeId);
    }

    /**
     * @param Product $product
     * @param null $scopeId
     *
     * @return bool
     */
    public function isApplied($product, $scopeId = null)
    {
        if (!$this->isEnabled($scopeId)) {
            return false;
        }

        $categories = $this->getApplyCategory($scopeId);
        $category   = empty($categories) ?: count(array_intersect($product->getCategoryIds(), $categories));

        $type = in_array($product->getTypeId(), $this->getApplyProduct($scopeId), true);

        $path = $this->_getRequest()->getFullActionName();
        foreach ($this->applyPage->getOptionArray() as $key => $item) {
            if (strpos($path, $key) !== false) {
                $path = $key;
                break;
            }
        }
        if ($path === 'wishlist_index_configure') {
            $path = ApplyPage::PRODUCT;
        }

        $page = in_array($path, $this->getApplyPage($scopeId), true);

        return $category && $type && $page;
    }

    /**
     * @param float $amount
     * @param bool $includeContainer
     * @param null $scope
     * @param null $currency
     * @param int $precision
     *
     * @return float
     */
    public function formatPrice(
        $amount,
        $includeContainer = true,
        $scope = null,
        $currency = null,
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION
    ) {
        return $this->priceCurrency->format($amount, $includeContainer, $precision, $scope, $currency);
    }

    /**
     * @param float $amount
     * @param null $scope
     *
     * @return float|string
     */
    public function convertPrice($amount, $scope = null)
    {
        return $this->priceCurrency->convert($amount, $scope);
    }

    /**
     * @param Product $product
     *
     * @return array
     * @throws LocalizedException
     */
    public function getQtyOptions($product)
    {
        $options = $this->determineQtyOptions($product);

        if ($this->isAllowOpenQty()) {
            $options[] = [
                'value' => 0,
                'title' => __('Input Quantity')
            ];
        }

        return $options;
    }

    /**
     * @param Product|ProductInterface $product
     *
     * @return array
     * @throws LocalizedException
     */
    public function determineQtyOptions($product)
    {
        try {
            /** @var Product $product */
            $product = $this->productRepository->get($product->getSku());
        } catch (NoSuchEntityException $e) {
            $this->_logger->critical($e);
        }

        $options          = [];
        $stockQty         = $this->getMaxQtyConfigurableChildren($product);
        $maxStepValue     = $this->getMaxStepValue($stockQty);
        $type             = $product->getTypeId();
        $finalPrice       = $this->taxHelper->getTaxPrice(
            $product,
            $product->getPriceInfo()->getPrice('final_price')->getValue(),
            true
        );
        $tierPrices       = $product->getTierPrices();
        $specialPrice     = $this->taxHelper->getTaxPrice($product, $product->getSpecialPrice(), true);
        $specialPriceTo   = $product->getSpecialToDate();
        $specialPriceFrom = $product->getSpecialFromDate();
        $allGroupId       = $this->groupManagement->getAllCustomersGroup()->getId();
        $groupId          = $this->session->getCustomerGroupId();

        if ($this->getChangeQtyStep() === ChangeQtyStep::CUSTOM) {
            foreach ($this->getCustomStep() as $item) {
                if ($item > $maxStepValue) {
                    continue;
                }

                $options[] = [
                    'value' => $item,
                    'title' => $this->formatOptionTitle(
                        $item,
                        $type,
                        $finalPrice,
                        $tierPrices,
                        $specialPrice,
                        $specialPriceFrom,
                        $specialPriceTo,
                        $allGroupId,
                        $groupId,
                        $product
                    )
                ];
            }

            return $options;
        }

        $step = $this->getStepValue($product);
        if ($limit = $step * $this->getLimitNumberOption()) {
            $maxStepValue = min($maxStepValue, $limit);
        }

        for ($i = $step; $i <= $maxStepValue; $i += $step) {
            $options[] = [
                'value' => $i,
                'title' => $this->formatOptionTitle(
                    $i,
                    $type,
                    $finalPrice,
                    $tierPrices,
                    $specialPrice,
                    $specialPriceFrom,
                    $specialPriceTo,
                    $allGroupId,
                    $groupId,
                    $product
                )
            ];
        }

        return $options;
    }

    /**
     * @param int $stockQty
     *
     * @return float|int
     */
    public function getMaxStepValue($stockQty)
    {
        switch ($this->getMaxQtyStep()) {
            case MaxQtyStep::STOCK:
                $maxStepValue = $stockQty;
                break;
            case MaxQtyStep::FIXED:
                $maxStepValue = $this->getMaxQtyValue();
                break;
            default:
                $maxStepValue = min($stockQty, $this->getMaxQtyValue());
                break;
        }

        return $maxStepValue;
    }

    /**
     * @param Product $product
     *
     * @return float
     */
    public function getStepValue($product)
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());

        switch ($this->getChangeQtyStep()) {
            case ChangeQtyStep::PRODUCT:
                $step = $stockItem->getQtyIncrements() ?: 1;
                break;
            case ChangeQtyStep::FIXED:
                $step = $this->getQtyStepValue();
                break;
            case ChangeQtyStep::CUSTOM:
            default:
                $step = self::jsonEncode($this->getCustomStep());
                break;
        }

        if (!$step && $this->getChangeQtyStep() === ChangeQtyStep::FIXED) {
            $step = 1;
        }

        return (float) $step;
    }

    /**
     * @param int $qty
     * @param string $type
     * @param float $finalPrice
     * @param ProductTierPriceInterface[]|null $tierPrices
     * @param float $specialPrice
     * @param float $specialPriceFrom
     * @param float $specialPriceTo
     * @param int $allGroupId
     * @param int $groupId
     * @param Product $product
     *
     * @return string|string[]
     */
    public function formatOptionTitle(
        $qty,
        $type,
        $finalPrice,
        $tierPrices,
        $specialPrice,
        $specialPriceFrom,
        $specialPriceTo,
        $allGroupId,
        $groupId,
        $product
    ) {
        $percent   = '0%';
        $totalTmpl = $this->formatPrice($finalPrice, false);
        $priceTmpl = $totalTmpl;
        if ($qty === 1) {
            $title = $this->getOptionTemplate();

            return str_replace(
                ['{{qty}}', '{{price}}', '{{total}}', '{{percent}}'],
                [$qty, $priceTmpl, $totalTmpl, $percent],
                $title
            );
        }

        $title = $this->getOptionTemplateMultiQty();

        $totalTmpl = $this->formatPrice($finalPrice * $qty, false);

        /** @var ProductTierPriceInterface $item */
        foreach ($tierPrices as $item) {
            if ($qty < $item->getQty() || !in_array($item->getCustomerGroupId(), [$allGroupId, $groupId])) {
                continue;
            }

            $value = $this->taxHelper->getTaxPrice($product, $item->getValue(), true);
            $price = $type === 'bundle' ? $finalPrice * (1 - $value / 100) : $value;

            $percent = $type === 'bundle' ? $value : (1 - $this->convertPrice($value) / $finalPrice) * 100;
            $percent = round($percent) . '%';

            $isAvailable = $this->timezone->isScopeDateInInterval(null, $specialPriceFrom, $specialPriceTo);
            if ($specialPrice && $isAvailable && $price > $specialPrice) {
                continue;
            }

            $price = $this->convertPrice($price);

            $totalTmpl = $this->formatPrice($price * $qty, false);
            $priceTmpl = $this->formatPrice($price, false);

            $title = $this->getOptionTemplateTierPrice();
        }

        return str_replace(
            ['{{qty}}', '{{price}}', '{{total}}', '{{percent}}'],
            [$qty, $priceTmpl, $totalTmpl, $percent],
            $title
        );
    }

    /**
     * @return string
     */
    public function getPriceFormat()
    {
        return self::jsonEncode($this->format->getPriceFormat());
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    public function getOptionMap($product)
    {
        if (!method_exists($product->getTypeInstance(), 'getConfigurableOptions')) {
            return [];
        }

        return array_keys($product->getTypeInstance()->getConfigurableOptions($product));
    }

    /**
     * @param Product $product
     *
     * @return array
     * @throws LocalizedException
     */
    public function getTierPrices($product)
    {
        $type = $product->getTypeId();

        $result = [];

        $allCustomersGroupId = $this->groupManagement->getAllCustomersGroup()->getId();
        $customerGroupId     = $this->session->getCustomerGroupId();
        /** @var ProductTierPriceInterface $item */
        foreach ($product->getTierPrices() as $item) {
            if (!in_array($item->getCustomerGroupId(), [$allCustomersGroupId, $customerGroupId])) {
                continue;
            }

            /** @var array $tierPrice */
            $tierPrice = $product->getTierPrice($item->getQty());
            if (is_array($tierPrice)) {
                $tierPrice = $tierPrice[0];
            }

            $value            = $this->taxHelper->getTaxPrice($product, $item->getValue(), true) ?: $tierPrice;
            $specialPrice     = $this->taxHelper->getTaxPrice($product, $product->getSpecialPrice(), true);
            $specialPriceTo   = $product->getSpecialToDate();
            $specialPriceFrom = $product->getSpecialFromDate();
            $isAvailable      = $this->timezone->isScopeDateInInterval(null, $specialPriceFrom, $specialPriceTo);
            if ($specialPrice && $isAvailable && $value > $specialPrice) {
                continue;
            }

            $result[] = [
                'qty'     => $item->getQty(),
                'value'   => $this->convertPrice($value),
                'percent' => $type === 'bundle' ? round($value) : 0
            ];
        }

        return $result;
    }

    /**
     * @param Product $product
     *
     * @return array
     * @throws LocalizedException
     */
    public function getConfigurableChildren($product)
    {
        if (!method_exists($product->getTypeInstance(), 'getConfigurableOptions')) {
            return [];
        }

        $result = [];

        $allCustomersGroupId = $this->groupManagement->getAllCustomersGroup()->getId();
        $customerGroupId     = $this->session->getCustomerGroupId();

        $data = $product->getTypeInstance()->getConfigurableOptions($product);
        foreach ((array) $data as $key => $datum) {
            foreach ((array) $datum as $item) {
                $sku                  = $item['sku'];
                $result[$sku][$key]   = $item['value_index'];
                $result[$sku]['tier'] = [];

                try {
                    /** @var Product $prod */
                    $prod = $this->productRepository->get($item['sku']);

                    $stockItem = $this->stockRegistry->getStockItem($prod->getId(), $prod->getStore()->getWebsiteId());

                    $result[$sku]['price'] = $this->convertPrice(
                        $this->taxHelper->getTaxPrice(
                            $prod,
                            $prod->getFinalPrice(),
                            true
                        )
                    );
                    $result[$sku]['qty']   = $this->getMaxStepValue($stockItem->getQty());
                    /** @var ProductTierPriceInterface $tierPrice */
                    foreach ($prod->getTierPrices() as $tierPrice) {
                        if (!in_array($tierPrice->getCustomerGroupId(), [$allCustomersGroupId, $customerGroupId])) {
                            continue;
                        }

                        $value            = $this->taxHelper->getTaxPrice($prod, $tierPrice->getValue(), true);
                        $specialPrice     = $this->taxHelper->getTaxPrice($prod, $prod->getSpecialPrice(), true);
                        $finalPrice       = $this->taxHelper->getTaxPrice($prod, $prod->getFinalPrice(), true);
                        $specialPriceTo   = $prod->getSpecialToDate();
                        $specialPriceFrom = $prod->getSpecialFromDate();
                        $isAvailable      = $this->timezone->isScopeDateInInterval(
                            null,
                            $specialPriceFrom,
                            $specialPriceTo
                        );
                        if ($specialPrice && $isAvailable && $value > $specialPrice) {
                            continue;
                        }

                        $result[$sku]['tier'][] = [
                            'qty'     => (float) $tierPrice->getQty(),
                            'value'   => $this->convertPrice($value),
                            'percent' => 100 - $value / $finalPrice * 100
                        ];
                    }
                } catch (NoSuchEntityException $e) {
                    $this->_logger->critical($e);
                }
            }
        }

        return $result;
    }

    /**
     * @param Product|ProductInterface $product
     *
     * @return array
     */
    public function getMaxQtyConfigurableChildren($product)
    {
        try {
            /** @var Product $product */
            $product = $this->productRepository->get($product->getSku());
        } catch (NoSuchEntityException $e) {
            $this->_logger->critical($e);
        }

        $prodId  = $product->getId();
        $scopeId = $product->getStore()->getWebsiteId();

        $stockItem = $this->stockRegistry->getStockItem($prodId, $scopeId);
        $stockQty  = $stockItem->getQty() ?: 0;

        if (count($childrenIds = $product->getTypeInstance()->getChildrenIds($prodId))) {
            foreach ($childrenIds as $children) {
                foreach ((array) $children as $childId) {
                    $item = $this->stockRegistry->getStockItem($childId, $scopeId);
                    $qty  = $item->getQty();
                    if ($this->versionCompare('2.3.0')) {
                        try {
                            /** @var Product $childProduct */
                            $childProduct = $this->productRepository->getById($childId);
                        } catch (NoSuchEntityException $e) {
                            $this->_logger->critical($e);
                        }
                        $salableQuantity = $this->objectManager->create(
                            \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku::class
                        );
                        $salable         = $salableQuantity->execute($childProduct->getSku());
                        $qty             = $salable[0]['qty'];
                    }
                    $stockQty = max($stockQty, $qty);
                }
            }
        } else {
            if ($this->versionCompare('2.3.0')) {
                $salableQuantity = $this->objectManager->create(
                    \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku::class
                );
                $salable         = $salableQuantity->execute($product->getSku());
                $stockQty        = $salable[0]['qty'];
            }
        }

        return $stockQty;
    }

    /**
     * @param array $quantityValidators
     *
     * @return array
     */
    public function determineValidators($quantityValidators)
    {
        if (!empty($quantityValidators['validate-item-quantity']['qtyIncrements'])
            && $this->getChangeQtyStep() !== ChangeQtyStep::PRODUCT) {
            unset($quantityValidators['validate-item-quantity']['qtyIncrements']);
        }

        return $quantityValidators;
    }

    /**
     * @return TaxHelper
     */
    public function getTaxHelper()
    {
        return $this->taxHelper;
    }
}
