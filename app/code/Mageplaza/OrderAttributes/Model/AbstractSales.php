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

namespace Mageplaza\OrderAttributes\Model;

use Exception;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

/**
 * Class AbstractSales
 * @package Mageplaza\OrderAttributes\Model
 */
abstract class AbstractSales extends AbstractModel
{
    /**
     * @param Attribute $attribute
     *
     * @return $this
     * @throws LocalizedException
     */
    public function createAttribute(Attribute $attribute)
    {
        $this->_getResource()->createAttribute($attribute);

        return $this;
    }

    /**
     * @param Attribute $attribute
     *
     * @return $this
     * @throws LocalizedException
     */
    public function deleteAttribute(Attribute $attribute)
    {
        $this->_getResource()->deleteAttribute($attribute);

        return $this;
    }

    /**
     * @param int|string $id
     * @param array $data
     *
     * @return $this
     * @throws Exception
     */
    public function saveAttributeData($id, $data)
    {
        $this->addData($data)->setId($id)->save();

        return $this;
    }

    /**
     * @param DataObject[] $entities
     *
     * @return array
     * @throws LocalizedException
     */
    public function attachDataToSalesOrder(array $entities)
    {
        return $this->_getResource()->attachDataToSalesOrder($entities);
    }
}
