<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Pricing\Render;

use Amasty\GiftCard\Model\Config\Source\Fee;
use Amasty\GiftCard\ViewModel\Price\Component\PriceFactory;
use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface;
use Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface;
use Magento\Catalog\Pricing\Render\FinalPriceBox as RenderPrice;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\View\Element\Template\Context;

class FinalPriceBox extends RenderPrice
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var float[]
     */
    private $minMaxPrice;

    public function __construct(
        Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        PriceCurrencyInterface $priceCurrency,
        array $data = [],
        SalableResolverInterface $salableResolver = null,
        MinimalPriceCalculatorInterface $minimalPriceCalculator = null
    ) {
        parent::__construct(
            $context,
            $saleableItem,
            $price,
            $rendererPool,
            $data,
            $salableResolver,
            $minimalPriceCalculator
        );
        $this->priceCurrency = $priceCurrency;
        $this->initializeMinMaxPrice();
    }

    /**
     * Initialize min and max price of product
     * depending of allowed amounts
     */
    protected function initializeMinMaxPrice()
    {
        $min = $max = null;

        if ((bool)$this->saleableItem->getAmAllowOpenAmount()) {
            $min = (float)$this->saleableItem->getAmOpenAmountMin() ?: 1; //to show price 'from 1' on catalog
            $max = (float)$this->saleableItem->getAmOpenAmountMax() ?: 0;
        }

        foreach ((array)$this->saleableItem->getAmGiftcardPrices() as $amount) {
            $min = $min === null ? $amount['value'] : min($min, $amount['value']);
            $max = $max === null ? $amount['value'] : max($max, $amount['value']);
        }
        $this->minMaxPrice = ['min' => (float)$min, 'max' => (float)$max];
    }

    /**
     * @return float
     */
    public function getMinPrice(): float
    {
        return $this->minMaxPrice['min'];
    }

    /**
     * @return float
     */
    public function getMaxPrice(): float
    {
        return $this->minMaxPrice['max'];
    }

    /**
     * @return bool
     */
    public function isSinglePrice(): bool
    {
        return $this->minMaxPrice['min'] && $this->minMaxPrice['max']
            && $this->minMaxPrice['min'] === $this->minMaxPrice['max'];
    }

    /**
     * @return float
     */
    public function getFinalPrice(): float
    {
        $product = $this->getSaleableItem();

        $customValue = $this->getMinPrice();
        if ($product->getAmGiftcardFeeEnable()) {
            $feeType = $product->getAmGiftcardFeeType();
            $feeValue = $product->getAmGiftcardFeeValue();

            if ($feeType && $feeValue) {
                switch ($feeType) {
                    case Fee::PRICE_TYPE_PERCENT:
                        $customValue += $customValue * $feeValue / 100;
                        break;
                    case Fee::PRICE_TYPE_FIXED:
                        $customValue += $feeValue;
                        break;
                }
            }
        }

        return $customValue;
    }

    /**
     * @param float $amount
     * @param bool $includeContainer
     * @return string
     */
    public function convertAndFormatCurrency(float $amount, bool $includeContainer = true): string
    {
        return $this->priceCurrency->convertAndFormat($amount, $includeContainer);
    }

    /**
     * @param float|null $amount
     * @return float|null
     */
    public function convertCurrency(?float $amount): float
    {
        return $this->priceCurrency->convert($amount);
    }

    public function isProductForm(): bool
    {
        return (bool)$this->getData('is_product_from');
    }
}
