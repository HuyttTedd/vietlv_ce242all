<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-stability
 * @version   1.1.0
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\StabilityError\Model;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\StabilityError\Api\Data\ErrorInterface;

class Error extends AbstractModel implements ErrorInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Error::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getSnapshotId()
    {
        return $this->getData(self::SNAPSHOT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSnapshotId($value)
    {
        return $this->setData(self::SNAPSHOT_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->getData(self::IDENTIFIER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentifier($value)
    {
        return $this->setData(self::IDENTIFIER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($value)
    {
        return $this->setData(self::TYPE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUri()
    {
        return $this->getData(self::URI);
    }

    /**
     * {@inheritdoc}
     */
    public function setUri($value)
    {
        return $this->setData(self::URI, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($value)
    {
        return $this->setData(self::MESSAGE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTrace()
    {
        return $this->getData(self::TRACE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTrace($value)
    {
        return $this->setData(self::TRACE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCount()
    {
        return $this->getData(self::COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCount($value)
    {
        return $this->setData(self::COUNT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }
}
