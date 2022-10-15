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

use Magento\Catalog\Controller\Adminhtml\Product\Builder as CatalogBuilder;
use Magento\Framework\App\RequestInterface;

/**
 * Class Builder
 * @package Mageplaza\BetterTierPrice\Plugin\Catalog\Controller\Adminhtml\Product
 */
class Builder
{
    /**
     * @param CatalogBuilder $subject
     * @param RequestInterface $request
     *
     * @return array
     */
    public function beforeBuild(
        CatalogBuilder $subject,
        RequestInterface $request
    ) {
        $params = $request->getPost('product');

        if (!isset($params['mp_specific_customer'])) {
            $params['mp_specific_customer'] = [];
            $request->setPostValue('product', $params);
        }

        return [$request];
    }
}
