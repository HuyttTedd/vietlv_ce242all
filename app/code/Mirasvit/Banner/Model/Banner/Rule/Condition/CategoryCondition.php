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

/**
 * @method getAttribute()
 * @method getJsFormObject()
 */
class CategoryCondition extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{
    public function loadAttributeOptions()
    {
        $attributes['category_ids'] = __('Category');

        $this->setData('attribute_option', $attributes);

        return $this;
    }

    public function getNewChildSelectOptions()
    {
        $attributes = [];

        foreach ($this->loadAttributeOptions()->getData('attribute_option') as $code => $label) {
            $attributes[] = [
                'value' => CategoryCondition::class . '|' . $code,
                'label' => $label,
            ];
        }

        return $attributes;
    }

    /**
     * @param AbstractModel $object
     *
     * @return bool
     */
    public function validate(AbstractModel $object)
    {
        $category = $object->getData('category');

        if (!$category) {
            return false;
        }

        $attr = $this->getAttribute();
        if ($attr === 'category_ids') {
            $attr = 'entity_id';
        }

        $value = $category->getData($attr);

        return $this->validateAttribute($value);
    }
}
