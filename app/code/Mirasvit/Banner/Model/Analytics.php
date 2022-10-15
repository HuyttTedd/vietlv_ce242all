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
 * @package   mirasvit/module-banner
 * @version   1.0.11
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Banner\Model;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\Banner\Api\Data\AnalyticsInterface;

class Analytics extends AbstractModel implements AnalyticsInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Analytics::class);
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
    public function getBannerId()
    {
        return $this->getData(self::BANNER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setBannerId($value)
    {
        return $this->setData(self::BANNER_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return $this->getData(self::ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setAction($value)
    {
        return $this->setData(self::ACTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getReferrer()
    {
        return $this->getData(self::REFERRER);
    }

    /**
     * {@inheritdoc}
     */
    public function setReferrer($value)
    {
        return $this->setData(self::REFERRER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSessionId()
    {
        return $this->getData(self::SESSION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSessionId($value)
    {
        return $this->setData(self::SESSION_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRemoteAddr()
    {
        return $this->getData(self::REMOTE_ADDR);
    }

    /**
     * {@inheritdoc}
     */
    public function setRemoteAddr($value)
    {
        return $this->setData(self::REMOTE_ADDR, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }
}
