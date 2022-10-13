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

namespace Mageplaza\BetterChangeQty\Plugin\Model;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Mageplaza\BetterChangeQty\Helper\Data;
use Mageplaza\BetterChangeQty\Model\Config\Source\ChangeQtyStep;

/**
 * Class StockStateProvider
 *
 * @package Mageplaza\BetterChangeQty\Plugin\Model
 */
class StockStateProvider
{
    /**
     * @var Data
     */
    protected $data;

    /**
     * StockStateProvider constructor.
     *
     * @param Data $data
     */
    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    /**
     * @param \Magento\CatalogInventory\Model\StockStateProvider $subject
     * @param StockItemInterface $stockItem
     * @param float|int $qty
     *
     * @return array
     * @SuppressWarnings(Unused)
     */
    public function beforeCheckQtyIncrements(
        \Magento\CatalogInventory\Model\StockStateProvider $subject,
        StockItemInterface $stockItem,
        $qty
    ) {
        if ($this->data->isEnabled() && $this->data->getChangeQtyStep() != ChangeQtyStep::PRODUCT) {
            $stockItem->setSuppressCheckQtyIncrements(true);
        }

        return [$stockItem, $qty];
    }
}
