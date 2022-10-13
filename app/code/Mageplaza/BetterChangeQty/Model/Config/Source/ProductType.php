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

namespace Mageplaza\BetterChangeQty\Model\Config\Source;

use Magento\Catalog\Model\Product\Type;

/**
 * Class ProductType
 *
 * @package Mageplaza\BetterChangeQty\Model\Config\Source
 */
class ProductType extends Type
{
    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionArray()
    {
        $options = parent::getOptionArray();

        if (isset($options['downloadable'])) {
            unset($options['downloadable']);
        }

        return $options;
    }
}
