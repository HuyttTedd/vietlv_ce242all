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
use Mirasvit\Banner\Api\Data\PlaceholderInterface;

class Placeholder extends AbstractModel implements PlaceholderInterface
{
    /**
     * @var Placeholder\RuleFactory
     */
    private $ruleFactory;

    /**
     * @var Placeholder\Rule
     */
    private $rule;

    public function __construct(
        Placeholder\RuleFactory $ruleFactory,
        Context $context,
        Registry $registry
    ) {
        $this->ruleFactory = $ruleFactory;

        parent::__construct($context, $registry);
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\Placeholder::class);
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

    public function getRenderer()
    {
        return $this->getData(self::RENDERER);
    }

    public function setRenderer($value)
    {
        return $this->setData(self::RENDERER, $value);
    }

    public function getLayoutUpdateId()
    {
        return $this->getData(self::LAYOUT_UPDATE_ID);
    }

    public function setLayoutUpdateId($value)
    {
        return $this->setData(self::LAYOUT_UPDATE_ID, $value);
    }

    public function getLayoutPosition()
    {
        return $this->getData(self::LAYOUT_POSITION);
    }

    public function setLayoutPosition($value)
    {
        return $this->setData(self::LAYOUT_POSITION, $value);
    }

    public function getPositionLayout()
    {
        return $this->getLayoutPositionPart(self::POSITION_LAYOUT);
    }

    public function setPositionLayout($value)
    {
        return $this->setLayoutPositionPart(self::POSITION_LAYOUT, $value);
    }

    public function getPositionContainer()
    {
        return $this->getLayoutPositionPart(self::POSITION_CONTAINER);
    }

    public function setPositionContainer($value)
    {
        return $this->setLayoutPositionPart(self::POSITION_CONTAINER, $value);
    }

    public function getPositionBefore()
    {
        return $this->getLayoutPositionPart(self::POSITION_BEFORE);
    }

    public function setPositionBefore($value)
    {
        return $this->setLayoutPositionPart(self::POSITION_BEFORE, $value);
    }

    public function getPositionAfter()
    {
        return $this->getLayoutPositionPart(self::POSITION_AFTER);
    }

    public function setPositionAfter($value)
    {
        return $this->setLayoutPositionPart(self::POSITION_AFTER, $value);
    }

    public function getConditions()
    {
        return $this->getData(self::CONDITIONS_SERIALIZED);
    }

    public function setConditions($value)
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $value);
    }

    /**
     * @return Placeholder\Rule
     */
    public function getRule()
    {
        if (!$this->rule) {
            $this->rule = $this->ruleFactory->create()
                ->setData(self::CONDITIONS_SERIALIZED, $this->getData(self::CONDITIONS_SERIALIZED));
        }

        return $this->rule;
    }

    public function getCss()
    {
        return $this->getData(self::CSS);
    }

    public function setCss($value)
    {
        return $this->setData(self::CSS, $value);
    }

    public function getIdentifier()
    {
        return $this->getData(self::IDENTIFIER);
    }

    public function setIdentifier($value)
    {
        return $this->setData(self::IDENTIFIER, $value);
    }

    /**
     * @param string $part
     *
     * @return array|string
     */
    private function getLayoutPositionPart($part = '')
    {
        $arr = explode('/', $this->getLayoutPosition());

        $position = [
            self::POSITION_LAYOUT    => isset($arr[0]) ? $arr[0] : '',
            self::POSITION_CONTAINER => isset($arr[1]) ? $arr[1] : '',
            self::POSITION_BEFORE    => isset($arr[2]) ? $arr[2] : '',
            self::POSITION_AFTER     => isset($arr[3]) ? $arr[3] : '',
        ];

        return $part ? $position[$part] : $position;
    }

    /**
     * @param string $part
     * @param string $value
     *
     * @return $this
     */
    private function setLayoutPositionPart($part, $value)
    {
        $parts        = $this->getLayoutPositionPart();
        $parts[$part] = $value;

        return $this->setLayoutPosition(implode('/', $parts));
    }

}
