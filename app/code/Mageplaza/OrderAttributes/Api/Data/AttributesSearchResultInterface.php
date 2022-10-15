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
 * Interface AttributesSearchResultInterface
 * @package Mageplaza\OrderAttributes\Api\Data
 */
interface AttributesSearchResultInterface
{
    /**
     * @return \Mageplaza\OrderAttributes\Api\Data\AttributesInterface[]
     */
    public function getItems();

    /**
     * @param \Mageplaza\OrderAttributes\Api\Data\AttributesInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
