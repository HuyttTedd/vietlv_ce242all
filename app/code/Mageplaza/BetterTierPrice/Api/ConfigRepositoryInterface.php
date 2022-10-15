<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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
namespace Mageplaza\BetterTierPrice\Api;

/**
 * Interface ConfigRepositoryInterface
 * @package Mageplaza\BetterTierPrice\Api
 */
interface ConfigRepositoryInterface
{
    /**
     * @param string|null $storeId
     *
     * @return \Mageplaza\BetterTierPrice\Api\Data\ConfigInterface
     * @return mixed
     */
    public function getConfigs($storeId = null);
}
