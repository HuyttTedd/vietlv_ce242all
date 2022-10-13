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
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Mirasvit\Banner\Api\Data\BannerInterface;

class Banner extends AbstractModel implements BannerInterface
{
    /**
     * @var Banner\RuleFactory
     */
    private $ruleFactory;

    /**
     * @var Banner\Rule
     */
    private $rule;

    public function __construct(
        Banner\RuleFactory $ruleFactory,
        Context $context,
        Registry $registry
    ) {
        $this->ruleFactory = $ruleFactory;

        parent::__construct($context, $registry);
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\Banner::class);
    }

    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    public function isActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    public function setIsActive($value)
    {
        return $this->setData(self::IS_ACTIVE, $value);
    }

    public function getActiveFrom()
    {
        return $this->getData(self::ACTIVE_FROM);
    }

    public function setActiveFrom($value)
    {
        return $this->setData(self::ACTIVE_FROM, $value ? $value : null);
    }

    public function getActiveTo()
    {
        return $this->getData(self::ACTIVE_TO);
    }

    public function setActiveTo($value)
    {
        return $this->setData(self::ACTIVE_TO, $value ? $value : null);
    }

    public function getPlaceholderIds()
    {
        return explode(',', $this->getData(self::PLACEHOLDER_IDS));
    }

    public function setPlaceholderIds(array $value)
    {
        return $this->setData(self::PLACEHOLDER_IDS, implode(',', $value));
    }

    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    public function setSortOrder($value)
    {
        return $this->setData(self::SORT_ORDER, $value);
    }

    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    public function setContent($value)
    {
        return $this->setData(self::CONTENT, $value);
    }

    public function getUrl()
    {
        return $this->getData(self::URL);
    }

    public function setUrl($value)
    {
        return $this->setData(self::URL, $value);
    }

    public function getConditions()
    {
        return $this->getData(self::CONDITIONS_SERIALIZED);
    }

    public function setConditions($value)
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $value);
    }

    public function getCustomerGroupIds()
    {
        return explode(',', $this->getData(self::CUSTOMER_GROUP_IDS));
    }

    public function setCustomerGroupIds(array $value)
    {
        return $this->setData(self::CUSTOMER_GROUP_IDS, implode(',', $value));
    }

    public function getStoreIds()
    {
        return explode(',', $this->getData(self::STORE_IDS));
    }

    public function setStoreIds(array $value)
    {
        return $this->setData(self::STORE_IDS, implode(',', $value));
    }

    /**
     * @return Banner\Rule
     */
    public function getRule()
    {
        if (!$this->rule) {
            $this->rule = $this->ruleFactory->create()
                ->setData(self::CONDITIONS_SERIALIZED, $this->getData(self::CONDITIONS_SERIALIZED));
        }

        return $this->rule;
    }
}
