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

namespace Mageplaza\BetterChangeQty\Plugin\Model\Product\Type;

use Mageplaza\BetterChangeQty\Helper\Data;

/**
 * Class Configurable
 *
 * @package Mageplaza\BetterChangeQty\Plugin\Model\Product\Type
 */
class Configurable
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
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $subject
     * @param $result
     *
     * @return bool
     * @SuppressWarnings(Unused)
     */
    public function afterHasRequiredOptions(
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $subject,
        $result
    ) {
        if ($this->data->isEnabled()) {
            return false;
        }

        return $result;
    }
}
