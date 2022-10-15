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

namespace Mageplaza\BetterTierPrice\Plugin\Catalog\Model\ResourceModel\Product\Attribute\Backend;

use Magento\Framework\Db\Select;
use Mageplaza\BetterTierPrice\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice as CatalogTierPrice;

/**
 * Class Tierprice
 * @package Mageplaza\BetterTierPrice\Plugin\Catalog\Model\ResourceModel\Product\Attribute\Backend
 */
class Tierprice
{
    /**
     * @var Data
     */
    private $helperData;

    /**
     * Tierprice constructor.
     *
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param CatalogTierPrice $tierprice
     * @param Select $select
     *
     * @return mixed
     */
    public function afterGetSelect(
        CatalogTierPrice $tierprice,
        $select
    ) {
        if (!$this->helperData->isEnabled()) {
            return $select;
        }
        $select->columns(['mp_discount_fixed', 'mp_discount_fixed']);

        return $select;
    }
}
