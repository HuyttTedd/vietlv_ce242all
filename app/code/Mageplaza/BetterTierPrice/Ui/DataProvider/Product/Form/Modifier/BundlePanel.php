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

namespace Mageplaza\BetterTierPrice\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Config\Source\ProductPriceOptionsInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class BundlePanel
 * @package Mageplaza\BetterTierPrice\Ui\DataProvider\Product\Form\Modifier
 */
class BundlePanel extends AbstractModifier
{
    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * BundlePanel constructor.
     *
     * @param ArrayManager $arrayManager
     */
    public function __construct(ArrayManager $arrayManager)
    {
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->removeFixedTierPrice($meta);
        $meta = $this->removeFixedTierPriceForSpecificCustomer($meta);

        return $meta;
    }

    /**
     * Remove option with fixed/discount-fixed tier price from config.
     *
     * @param array $meta
     *
     * @return array
     */
    private function removeFixedTierPrice(array $meta)
    {
        $tierPricePath = $this->arrayManager->findPath(
            ProductAttributeInterface::CODE_TIER_PRICE,
            $meta,
            null,
            'children'
        );
        $pricePath     = $this->arrayManager->findPath(
            ProductAttributeInterface::CODE_TIER_PRICE_FIELD_PRICE,
            $meta,
            $tierPricePath
        );
        $pricePath     = $this->arrayManager->slicePath($pricePath, 0, -1) . '/value_type/arguments/data/options';

        $price = $this->arrayManager->get($pricePath, $meta);
        if ($price) {
            $meta = $this->arrayManager->remove($pricePath, $meta);
            foreach ($price as $key => $item) {
                if ($item['value'] === ProductPriceOptionsInterface::VALUE_FIXED) {
                    unset($price[$key]);
                }
                if ($item['value'] === 'mp_discount_fixed') {
                    unset($price[$key]);
                }
            }
            $meta = $this->arrayManager->merge(
                $this->arrayManager->slicePath($pricePath, 0, -1),
                $meta,
                ['options' => $price]
            );
        }

        return $meta;
    }

    /**
     * Remove option with fixed/discount-fixed for specific customer tier price from config.
     *
     * @param array $meta
     *
     * @return array
     */
    private function removeFixedTierPriceForSpecificCustomer(array $meta)
    {
        $specificCustomerPath = $this->arrayManager->findPath(
            'mp_specific_customer',
            $meta,
            null,
            'children'
        );

        $pricePath = $this->arrayManager->findPath(
            ProductAttributeInterface::CODE_TIER_PRICE_FIELD_PRICE,
            $meta,
            $specificCustomerPath
        );
        $pricePath = $this->arrayManager->slicePath($pricePath, 0, -1) . '/value_type/arguments/data/options';

        $price = $this->arrayManager->get($pricePath, $meta);
        if ($price) {
            $meta = $this->arrayManager->remove($pricePath, $meta);
            foreach ($price as $key => $item) {
                if ($item['value'] === ProductPriceOptionsInterface::VALUE_FIXED) {
                    unset($price[$key]);
                }
                if ($item['value'] === 'mp_discount_fixed') {
                    unset($price[$key]);
                }
            }
            $meta = $this->arrayManager->merge(
                $this->arrayManager->slicePath($pricePath, 0, -1),
                $meta,
                ['options' => $price]
            );
        }

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}
