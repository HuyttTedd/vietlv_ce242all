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

namespace Mageplaza\BetterTierPrice\Api\Data;

/**
 * Interface ConfigInterface
 * @package Mageplaza\BetterTierPrice\Api\Data
 */
interface ConfigInterface
{
    const GENERAL  = 'general';

    /**
     * @return \Mageplaza\BetterTierPrice\Api\Data\Config\GeneralInterface
     */
    public function getGeneral();

    /**
     * @param \Mageplaza\BetterTierPrice\Api\Data\Config\GeneralInterface $value
     *
     * @return $this
     */
    public function setGeneral($value);
}
