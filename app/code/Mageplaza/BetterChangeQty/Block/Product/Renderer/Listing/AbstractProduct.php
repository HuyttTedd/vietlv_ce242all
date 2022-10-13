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

namespace Mageplaza\BetterChangeQty\Block\Product\Renderer\Listing;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Block\Product\AbstractProduct as AbstractProductCatalog;

/**
 * Class AbstractProduct
 * @package Mageplaza\BetterChangeQty\Block\Product\Renderer\Listing
 *
 * @method void setProduct(Product $product)
 */
class AbstractProduct extends AbstractProductCatalog
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_BetterChangeQty::product/listing/renderer.phtml';

    /**
     * Get default qty - either as preconfigured, or as 1.
     * Also restricts it by minimal qty.
     *
     * @param null|Product $product
     *
     * @return int|float
     */
    public function getProductDefaultQty($product = null)
    {
        if (!$product) {
            $product = $this->getProduct();
        }

        $config    = $product->getPreconfiguredValues();
        $configQty = $config->getData('qty');
        $qty       = $this->getMinimalQty($product);

        return min($qty, $configQty);
    }

    /**
     * Get Validation Rules for Quantity field
     *
     * @return array
     */
    public function getQuantityValidators()
    {
        $validators                    = [];
        $validators['required-number'] = true;

        return $validators;
    }
}
