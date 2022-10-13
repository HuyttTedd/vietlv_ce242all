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

namespace Mageplaza\BetterTierPrice\Helper;

use Magento\Framework\DataObject;
use Mageplaza\Core\Helper\AbstractData;

/**
 * Class Data
 * @package Mageplaza\BetterTierPrice\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'mp_tier_price';

    /**
     * @param null|int|string $storeId
     *
     * @return mixed
     */
    public function isTabularEnabled($storeId = null)
    {
        return $this->getConfigGeneral('enabled_tabular', $storeId);
    }

    /**
     * @param null|int|string $storeId
     *
     * @return mixed
     */
    public function isAutoChange($storeId = null)
    {
        return $this->getConfigGeneral('auto_change', $storeId);
    }

    /**
     * @param null|int|string $storeId
     *
     * @return mixed
     */
    public function isApplicableOnly($storeId = null)
    {
        return $this->getConfigGeneral('applicable_only', $storeId);
    }

    /**
     * @param null|int|string $storeId
     *
     * @return mixed
     */
    public function isSpecificCustomerEnabled($storeId = null)
    {
        return $this->getConfigGeneral('enabled_specific_customer', $storeId);
    }

    /**
     * @param null|int|string $storeId
     *
     * @return mixed
     */
    public function getTierPriceTitle($storeId = null)
    {
        return $this->getConfigGeneral('title', $storeId);
    }

    /**
     * check duplicate values in array
     *
     * @param $array
     *
     * @return bool
     */
    public function hasDupes($array)
    {
        return count($array) !== count(array_unique($array));
    }

    /**
     * Validation
     *
     * @param $mes
     *
     * @return mixed
     */
    public function showError($mes)
    {
        $response = $this->getObject(DataObject::class);

        $response->setError(true);
        $response->setMessage(__($mes));

        return $response;
    }

    /**
     * Format percent
     *
     * @param float $percent
     *
     * @return string
     */
    public function formatPercent(float $percent): string
    {
        /*First rtrim - trim zeros. So, 10.00 -> 10.*/
        /*Second rtrim - trim dot. So, 10. -> 10*/
        return rtrim(
            rtrim(number_format($percent, 2), '0'),
            '.'
        );
    }
}
