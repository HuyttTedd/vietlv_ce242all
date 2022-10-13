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



namespace Mirasvit\Banner\Model\Banner\Rule\Condition;

use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\AbstractCondition;

/**
 * @method getAttribute()
 * @method getJsFormObject()
 */
class PageCondition extends AbstractCondition
{
    const DATA_URI         = 'uri';
    const DATA_ACTION_NAME = 'action_name';

    public function getNewChildSelectOptions()
    {
        $attributes = [];

        foreach ($this->loadAttributeOptions()->getData('attribute_option') as $code => $label) {
            $attributes[] = [
                'value' => PageCondition::class . '|' . $code,
                'label' => $label,
            ];
        }

        return $attributes;
    }

    /**
     * @return $this|AbstractCondition
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            self::DATA_ACTION_NAME => (string)__('Action Name'),
            self::DATA_URI         => (string)__('URI'),
        ];

        asort($attributes);
        $this->setData('attribute_option', $attributes);

        return $this;
    }

    public function getDefaultOperatorInputByType()
    {
        $result = parent::getDefaultOperatorInputByType();

        $result['string'] = ['{}', '!{}', '==', '!='];

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            default:
                return 'string';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            default:
                return 'text';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getValueParsed()
    {
        return $this->getData('value');
    }

    /**
     * @param AbstractModel $object
     *
     * @return bool
     */
    public function validate(AbstractModel $object)
    {
        $value = $object->getData($this->getAttribute());

        return $this->validateAttribute($value);
    }
}
