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

namespace Mageplaza\BetterTierPrice\Model\Config;

use Magento\Framework\DataObject;
use Mageplaza\BetterTierPrice\Api\Data\Config\GeneralInterface;

/**
 * Class General
 * @package Mageplaza\BetterTierPrice\Model\Config
 */
class General extends DataObject implements GeneralInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return $this->getData(self::ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($value)
    {
        $this->setData(self::ENABLED, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnableTabular()
    {
        return $this->getData(self::ENABLED_TABULAR);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnableTabular($value)
    {
        $this->setData(self::ENABLED_TABULAR, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAutoChange()
    {
        return $this->getData(self::AUTO_CHANGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAutoChange($value)
    {
        $this->setData(self::AUTO_CHANGE, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getApplicableOnly()
    {
        return $this->getData(self::APPLICABLE_ONLY);
    }

    /**
     * {@inheritdoc}
     */
    public function setApplicableOnly($value)
    {
        $this->setData(self::APPLICABLE_ONLY, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabledSpecificCustomer()
    {
        return $this->getData(self::ENABLED_SPECIFIC_CUSTOMER);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabledSpecificCustomer($value)
    {
        $this->setData(self::ENABLED_SPECIFIC_CUSTOMER, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($value)
    {
        $this->setData(self::TITLE, $value);

        return $this;
    }
}
