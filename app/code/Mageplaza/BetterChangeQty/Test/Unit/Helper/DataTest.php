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

namespace Mageplaza\BetterChangeQty\Test\Unit\Helper;

use Magento\Catalog\Api\Data\ProductTierPriceInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Pricing\PriceInfo\Base;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\BetterChangeQty\Helper\Data;
use Mageplaza\BetterChangeQty\Model\Config\Source\ApplyPage;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * Class DataTest
 * @package Mageplaza\BetterChangeQty\Test\Unit\Helper
 */
class DataTest extends PHPUnit_Framework_TestCase
{
    const CONFIG_MODULE_PATH     = 'mpbetterchangeqty';
    const DEFAULT_OPT_TMPL       = 'Buy {{qty}}';
    const DEFAULT_OPT_TMPL_MULTI = 'Buy {{qty}} for {{price}} each';
    const DEFAULT_OPT_TMPL_TIER  = 'Buy {{qty}} for {{price}} each and save {{percent}}';

    /**
     * @var Context|PHPUnit_Framework_MockObject_MockObject
     */
    private $context;

    /**
     * @var ApplyPage|PHPUnit_Framework_MockObject_MockObject
     */
    private $applyPage;

    /**
     * @var RequestInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var ObjectManagerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManager;

    /**
     * @var StoreManagerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManager;

    /**
     * @var StockRegistryInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $stockRegistry;

    /**
     * @var PriceCurrencyInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $priceCurrency;

    /**
     * @var ProductRepositoryInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $productRepository;

    /**
     * @var ScopeConfigInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfig;

    /**
     * @var TimezoneInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $timezone;

    /**
     * @var FormatInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $localeFormat;

    /**
     * @var Session|PHPUnit_Framework_MockObject_MockObject
     */
    private $session;

    /**
     * @var GroupManagementInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $groupManagement;

    /**
     * @var Data
     */
    private $object;

    protected function setUp()
    {
        $this->context           = $this->getMockBuilder(Context::class)->disableOriginalConstructor()->getMock();
        $this->applyPage         = $this->getMockBuilder(ApplyPage::class)->disableOriginalConstructor()->getMock();
        $this->objectManager     = $this->getMockBuilder(ObjectManagerInterface::class)->getMock();
        $this->storeManager      = $this->getMockBuilder(StoreManagerInterface::class)->getMock();
        $this->stockRegistry     = $this->getMockBuilder(StockRegistryInterface::class)->getMock();
        $this->priceCurrency     = $this->getMockBuilder(PriceCurrencyInterface::class)->getMock();
        $this->productRepository = $this->getMockBuilder(ProductRepositoryInterface::class)->getMock();
        $this->timezone          = $this->getMockBuilder(TimezoneInterface::class)->getMock();
        $this->localeFormat      = $this->getMockBuilder(FormatInterface::class)->getMock();
        $this->session           = $this->getMockBuilder(Session::class)->disableOriginalConstructor()->getMock();
        $this->groupManagement   = $this->getMockBuilder(GroupManagementInterface::class)->getMock();

        $this->scopeConfig = $this->getMockBuilder(ScopeConfigInterface::class)->getMock();
        $this->request     = $this->getMockBuilder(RequestInterface::class)->setMethods(['getFullActionName'])->getMockForAbstractClass();

        $this->context->expects($this->any())->method('getScopeConfig')->willReturn($this->scopeConfig);
        $this->context->expects($this->any())->method('getRequest')->willReturn($this->request);

        $this->object = new Data(
            $this->context,
            $this->applyPage,
            $this->objectManager,
            $this->storeManager,
            $this->stockRegistry,
            $this->priceCurrency,
            $this->productRepository,
            $this->timezone,
            $this->localeFormat,
            $this->session,
            $this->groupManagement
        );
    }

    /**
     * @param $type
     * @param $path
     * @param $categoryId
     *
     * @dataProvider getMockProductData
     */
    public function testIsApplied($type, $path, $categoryId)
    {
        /** @var Product|PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMockBuilder(Product::class)->disableOriginalConstructor()->getMock();
        $scopeId = 0;

        $isEnabled = true;

        $state = $this->getMockBuilder(State::class)->disableOriginalConstructor()->getMock();

        $this->objectManager->expects($this->any())->method('get')->with('Magento\Framework\App\State')->willReturn($state);

        $this->scopeConfig->expects($this->at(0))->method('getValue')->with(self::CONFIG_MODULE_PATH . '/general/enabled')->willReturn($isEnabled);
        $this->scopeConfig->expects($this->at(1))->method('getValue')->with(self::CONFIG_MODULE_PATH . '/general/apply_category')->willReturn($categoryId);
        $this->scopeConfig->expects($this->at(2))->method('getValue')->with(self::CONFIG_MODULE_PATH . '/general/apply_product')->willReturn($type);
        $this->scopeConfig->expects($this->at(3))->method('getValue')->with(self::CONFIG_MODULE_PATH . '/general/apply_page')->willReturn($path);

        $product->expects($this->any())->method('getCategoryIds')->willReturn([$categoryId]);
        $product->expects($this->any())->method('getTypeId')->willReturn($type);

        $this->request->expects($this->any())->method('getFullActionName')->willReturn($path);
        $this->applyPage->expects($this->atLeastOnce())->method('getOptionArray')->willReturn([$path]);

        $this->assertEquals(true, $this->object->isApplied($product, $scopeId));
    }

    /**
     * @param $step
     * @param $maxQtyStep
     * @param $maxStepValue
     * @param $allowOpenQty
     *
     * @dataProvider getMockQtyOptionData
     * @throws LocalizedException
     */
    public function testGetQtyOptionsWithoutTierPrices($step, $maxQtyStep, $maxStepValue, $allowOpenQty)
    {
        /** @var Product|PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMockBuilder(Product::class)->disableOriginalConstructor()->getMock();

        $this->prepareConfig($product, $step, $maxQtyStep, $maxStepValue);

        $type     = 'simple';
        $price    = 20;
        $currency = '$';
        $product->expects($this->any())->method('getTypeId')->willReturn($type);
        $priceInfo = $this->getMockBuilder(Base::class)->disableOriginalConstructor()->getMock();
        $product->method('getPriceInfo')->willReturn($priceInfo);
        $priceInterface = $this->getMockBuilder(PriceInterface::class)->getMock();
        $priceInfo->method('getPrice')->with('final_price')->willReturn($priceInterface);
        $priceInterface->method('getValue')->willReturn($price);
        $product->method('getTierPrices')->willReturn([]);

        $result = [];
        $count  = 0;

        $group = $this->getMockBuilder(GroupInterface::class)->getMock();
        $this->groupManagement->method('getAllCustomersGroup')->willReturn($group);
        for ($i = $step; $i <= min($maxStepValue, 100); $i += $step) {
            $this->priceCurrency
                ->expects($this->at($count++))
                ->method('format')
                ->with($price, false)
                ->willReturn($currency . $price);

            if ($i == 1) {
                $title = self::DEFAULT_OPT_TMPL;
            } else {
                $title = self::DEFAULT_OPT_TMPL_MULTI;

                $this->priceCurrency
                    ->expects($this->at($count++))
                    ->method('format')
                    ->with($price * $i, false)
                    ->willReturn($currency . ($price * $i));
            }

            $result[] = [
                'value' => $i,
                'title' => str_replace(
                    ['{{qty}}', '{{price}}', '{{total}}'],
                    [$i, $currency . $price, $currency . ($price * $i)],
                    $title
                )
            ];
        }

        if ($allowOpenQty) {
            $result[] = [
                'value' => 0,
                'title' => new Phrase('Input Quantity')
            ];
        }

        $this->assertEquals($result, $this->object->getQtyOptions($product));
    }

    /**
     * @param $step
     * @param $maxQtyStep
     * @param $maxStepValue
     * @param $allowOpenQty
     *
     * @dataProvider getMockQtyOptionData
     * @throws LocalizedException
     */
    public function testGetQtyOptionsWithTierPrices($step, $maxQtyStep, $maxStepValue, $allowOpenQty)
    {
        /** @var Product|PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getSku',
                'getId',
                'getStore',
                'getTypeInstance',
                'getTypeId',
                'getPriceInfo',
                'getTierPrices',
                'getTierPrice'
            ])
            ->getMock();

        $this->prepareConfig($product, $step, $maxQtyStep, $maxStepValue);

        $type  = 'simple';
        $price = 100;
        $product->expects($this->any())->method('getTypeId')->willReturn($type);
        $priceInfo = $this->getMockBuilder(Base::class)->disableOriginalConstructor()->getMock();
        $product->method('getPriceInfo')->willReturn($priceInfo);
        $priceInterface = $this->getMockBuilder(PriceInterface::class)->getMock();
        $priceInfo->method('getPrice')->with('final_price')->willReturn($priceInterface);
        $priceInterface->method('getValue')->willReturn($price);

        $tierQty   = 3;
        $tierPrice = $price * 0.3;
        $tierItem  = $this->getMockBuilder(ProductTierPriceInterface::class)->getMock();
        $product->method('getTierPrices')->willReturn([$tierItem]);
        $tierItem->method('getQty')->willReturn($tierQty);
        $tierItem->method('getValue')->willReturn($tierPrice);

        $result = [];
        $count  = 0;

        $group = $this->getMockBuilder(GroupInterface::class)->getMock();
        $this->groupManagement->method('getAllCustomersGroup')->willReturn($group);
        for ($i = $step; $i <= min($maxStepValue, 100); $i += $step) {
            $result[] = [
                'value' => $i,
                'title' => $this->formatOptionTitle($tierQty, $tierPrice, $i, $price, $count)
            ];
        }

        if ($allowOpenQty) {
            $result[] = [
                'value' => 0,
                'title' => new Phrase('Input Quantity')
            ];
        }

        $this->assertEquals($result, $this->object->getQtyOptions($product));
    }

    /**
     * @param $tierQty
     * @param $tierPrice
     * @param $i
     * @param $price
     * @param $count
     *
     * @return string
     */
    private function formatOptionTitle($tierQty, $tierPrice, $i, $price, &$count)
    {
        $percent  = '0%';
        $currency = '$';

        $priceTmpl = $totalTmpl = $currency . $price;

        $this->priceCurrency
            ->expects($this->at($count++))
            ->method('format')
            ->with($price, false)
            ->willReturn($currency . $price);

        if ($i == 1) {
            $title = self::DEFAULT_OPT_TMPL;

            return str_replace(
                ['{{qty}}', '{{price}}', '{{total}}', '{{percent}}'],
                [$i, $priceTmpl, $totalTmpl, $percent],
                $title
            );
        }

        $title = self::DEFAULT_OPT_TMPL_MULTI;

        $totalTmpl = $currency . ($price * $i);

        $this->priceCurrency
            ->expects($this->at($count++))
            ->method('format')
            ->with($price * $i, false)
            ->willReturn($totalTmpl);

        if ($i < $tierQty) {
            return str_replace(
                ['{{qty}}', '{{price}}', '{{total}}', '{{percent}}'],
                [$i, $priceTmpl, $totalTmpl, $percent],
                $title
            );
        }

        $title = self::DEFAULT_OPT_TMPL_TIER;

        $totalTmpl = $currency . ($tierPrice * $i);
        $priceTmpl = $currency . $tierPrice;

        $count++; // increase format count when converting
        $this->priceCurrency->expects($this->any())->method('convert')->with($tierPrice)->willReturn($tierPrice);

        $count++; // increase format count when converting
        $this->priceCurrency
            ->expects($this->at($count++))
            ->method('format')
            ->with($tierPrice * $i, false)
            ->willReturn($totalTmpl);

        $this->priceCurrency
            ->expects($this->at($count++))
            ->method('format')
            ->with($tierPrice, false)
            ->willReturn($priceTmpl);

        $percent = round(100 - $tierPrice / $price * 100) . '%';

        return str_replace(
            ['{{qty}}', '{{price}}', '{{total}}', '{{percent}}'],
            [$i, $priceTmpl, $totalTmpl, $percent],
            $title
        );
    }

    /**
     * @param Product|PHPUnit_Framework_MockObject_MockObject $product
     * @param $step
     * @param $maxQtyStep
     * @param $maxStepValue
     */
    private function prepareConfig($product, $step, $maxQtyStep, $maxStepValue)
    {
        $productId = 1;
        $scopeId   = 1;
        $sku       = 'sku';

        $product->method('getSku')->willReturn($sku);
        $this->productRepository->method('get')->with($sku)->willReturn($product);

        $store = $this->getMockBuilder(Store::class)->disableOriginalConstructor()->getMock();
        $product->method('getId')->willReturn($productId);
        $product->method('getStore')->willReturn($store);
        $store->method('getWebsiteId')->willReturn($scopeId);

        $stockItem = $this->getMockBuilder(StockItemInterface::class)->getMock();
        $this->stockRegistry->method('getStockItem')->with($productId, $scopeId)->willReturn($stockItem);

        $stockItem->method('getQty')->willReturn($maxStepValue);

        $configurable = $this->getMockBuilder(Configurable::class)->disableOriginalConstructor()->getMock();
        $product->method('getTypeInstance')->willReturn($configurable);
        $configurable->method('getChildrenIds')->willReturn([]);

        $state = $this->getMockBuilder(State::class)->disableOriginalConstructor()->getMock();
        $this->objectManager->expects($this->at(0))->method('get')->with('Magento\Framework\App\State')->willReturn($state);
        $state->method('getAreaCode')->willReturn(Area::AREA_FRONTEND);

        $changeQtyStep = 1;
        $this->scopeConfig
            ->expects($this->at(0))
            ->method('getValue')
            ->with(self::CONFIG_MODULE_PATH . '/general/max_qty_step')
            ->willReturn($maxQtyStep);
        $this->scopeConfig
            ->expects($this->at(1))
            ->method('getValue')
            ->with(self::CONFIG_MODULE_PATH . '/general/change_qty_step')
            ->willReturn($changeQtyStep);
        $this->scopeConfig
            ->expects($this->at(2))
            ->method('getValue')
            ->with(self::CONFIG_MODULE_PATH . '/general/change_qty_step')
            ->willReturn($changeQtyStep);
        //        $this->scopeConfig
        //            ->expects($this->at(3 + floor($maxStepValue / $step))) // 20
        //            ->method('getValue')
        //            ->with(self::CONFIG_MODULE_PATH . '/general/allow_open_qty')
        //            ->willReturn($allowOpenQty);

        $stockItem->method('getQtyIncrements')->willReturn($step);
    }

    /**
     * @return array
     */
    public function getMockProductData()
    {
        return [
            [
                'type'       => 'simple',
                'path'       => 'category',
                'categoryId' => 2
            ]
        ];
    }

    /**
     * @return array
     */
    public function getMockQtyOptionData()
    {
        return [
            [
                'step'         => 1,
                'maxQtyStep'   => 1,
                'maxStepValue' => 10,
                'allowOpenQty' => false
            ],
        ];
    }
}
