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

namespace Mageplaza\BetterTierPrice\Api\Data\Config;

/**
 * Interface GeneralInterface
 * @package Mageplaza\BetterTierPrice\Api\Data\Config
 */
interface GeneralInterface
{
    const ENABLED                   = 'enabled';
    const ENABLED_TABULAR           = 'enabled_tabular';
    const AUTO_CHANGE               = 'auto_change';
    const APPLICABLE_ONLY           = 'applicable_only';
    const ENABLED_SPECIFIC_CUSTOMER = 'enabled_specific_customer';
    const TITLE                     = 'title';

    /**
     * @return bool
     */
    public function getEnabled();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setEnabled($value);

    /**
     * @return bool
     */
    public function getEnableTabular();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setEnableTabular($value);

    /**
     * @return bool
     */
    public function getAutoChange();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setAutoChange($value);

    /**
     * @return bool
     */
    public function getApplicableOnly();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setApplicableOnly($value);

    /**
     * @return bool
     */
    public function getEnabledSpecificCustomer();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setEnabledSpecificCustomer($value);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTitle($value);
}
