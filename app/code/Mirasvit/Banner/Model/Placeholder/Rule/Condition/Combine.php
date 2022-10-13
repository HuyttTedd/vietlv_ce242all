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



namespace Mirasvit\Banner\Model\Placeholder\Rule\Condition;

use Magento\Rule\Model\Condition\Context;
use Mirasvit\Banner\Model\Banner\Rule\Condition\PageCondition;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var PageCondition
     */
    private $pageCondition;


    public function __construct(
        PageCondition $pageCondition,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->pageCondition = $pageCondition;

        $this->setData('type', self::class);
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $pageAttributes = $this->pageCondition->loadAttributeOptions()->getData('attribute_option');

        $attributes = [];

        foreach ($pageAttributes as $code => $label) {
            $attributes['page'][] = [
                'value' => PageCondition::class . '|' . $code,
                'label' => $label,
            ];
        }

        $conditions = [];

        $conditions = array_merge_recursive($conditions, [
            [
                'label' => __('Page Attributes'),
                'value' => $attributes['page'],
            ],
        ]);

        return $conditions;
    }
}
