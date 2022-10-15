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

use Magento\Framework\DataObject;
use Mageplaza\OrderAttributes\Api\Data\FileResultInterface;

/**
 * Class Attribute
 * @package Mageplaza\OrderAttributes\Model
 */
class FileResult extends DataObject implements FileResultInterface
{
    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        return $this->getData(self::ERROR) ?: '';
    }

    /**
     * {@inheritdoc}
     */
    public function setError($value)
    {
        return $this->setData(self::ERROR, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        return $this->getData(self::FILE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFile($value)
    {
        return $this->setData(self::FILE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setSize($value)
    {
        return $this->setData(self::SIZE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->getData(self::SIZE);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->getData(self::URL);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($value)
    {
        return $this->setData(self::URL, $value);
    }
}
