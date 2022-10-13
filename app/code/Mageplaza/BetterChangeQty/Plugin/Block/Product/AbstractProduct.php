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

namespace Mageplaza\BetterChangeQty\Plugin\Block\Product;

use Closure;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\BetterChangeQty\Block\Product\Renderer\Listing\AbstractProduct as ProductBlock;
use Mageplaza\BetterChangeQty\Helper\Data;

/**
 * Class AbstractProduct
 *
 * @package Mageplaza\BetterChangeQty\Plugin\Block\Product
 */
class AbstractProduct
{
    /**
     * @var Data
     */
    protected $data;

    /**
     * ListProduct constructor.
     *
     * @param Data $data
     */
    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    /**
     * @param \Magento\Catalog\Block\Product\AbstractProduct $subject
     * @param Closure $proceed
     * @param Product $product
     * @param bool $templateType
     * @param bool $displayIfNoReviews
     *
     * @return string
     * @SuppressWarnings(Unused)
     * @throws LocalizedException
     */
    public function aroundGetReviewsSummaryHtml(
        \Magento\Catalog\Block\Product\AbstractProduct $subject,
        Closure $proceed,
        Product $product,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        $html = $proceed($product, $templateType, $displayIfNoReviews);

        if (strpos($subject->getRequest()->getFullActionName(), 'catalog_category_view') === false
            || in_array($product->getTypeId(), Data::NOT_ALLOWED_IN_CATEGORY)
            || !$this->data->isApplied($product)) {
            return $html;
        }

        /** @var ProductBlock $block */
        $block = $subject->getLayout()->createBlock(ProductBlock::class);
        $block->setProduct($product);
        $html .= $block->toHtml();

        return $html;
    }
}
