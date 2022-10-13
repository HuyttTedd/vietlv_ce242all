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
 * @package     Mageplaza_OrderAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\OrderAttributes\Api\Data;

/**
 * Interface OrderAttributesInterface
 * @package Mageplaza\OrderAttributes\Api\Data
 */
interface OrderAttributesInterface
{
    const LABEL                  = 'label';
    const VALUE                  = 'value';
    const ATTRIBUTE_CODE         = 'attribute_code';
    const SHOW_IN_FRONTEND_ORDER = 'show_in_frontend_order';

    /**
     * @return string
     */
    public function getAttributeCode();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setAttributeCode($value);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setLabel($value);

    /**
     * @return string|string[]
     */
    public function getValue();

    /**
     * @param string|string[] $value
     *
     * @return $this
     */
    public function setValue($value);

    /**
     * @return int
     */
    public function getShowInFrontendOrder();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setShowInFrontendOrder($value);
}
