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

use Magento\CatalogRule\Model\Rule\Condition\Combine as CatalogRuleCombine;
use Magento\Rule\Model\Condition\Context;
use Magento\SalesRule\Model\Rule\Condition\Combine as SalesRuleCombine;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    private $pageCondition;

    private $categoryCondition;

    private $salesRuleCombine;

    private $catalogRuleCombine;

    public function __construct(
        PageCondition $pageCondition,
        CategoryCondition $categoryCondition,
        SalesRuleCombine $salesRuleCombine,
        CatalogRuleCombine $catalogRuleCombine,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->pageCondition      = $pageCondition;
        $this->categoryCondition  = $categoryCondition;
        $this->salesRuleCombine   = $salesRuleCombine;
        $this->catalogRuleCombine = $catalogRuleCombine;

        $this->setData('type', self::class);
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $pageAttributes = $this->pageCondition->getNewChildSelectOptions();

        $categoryAttributes = $this->categoryCondition->getNewChildSelectOptions();

        $productAttributes = $this->catalogRuleCombine->getNewChildSelectOptions();
        if (count($productAttributes) === 3) {
            unset($productAttributes[0]);
            unset($productAttributes[1]);
            $productAttributes = $productAttributes[2]['value'];
        }

        $cartAttributes = $this->salesRuleCombine->getNewChildSelectOptions();
        unset($cartAttributes[0]);

        $pool = [
            'Page Attribute'       => $pageAttributes,
            'Category Attribute'   => $categoryAttributes,
            'Product Attribute'    => $productAttributes,
            'Cart Items Attribute' => $cartAttributes,
        ];

        $conditions = [
            [
                'value' => self::class,
                'label' => __('Conditions Combination'),
            ],
        ];

        foreach ($pool as $label => $value) {
            $conditions = array_merge_recursive($conditions, [
                [
                    'label' => __($label),
                    'value' => $value,
                ],
            ]);
        }

        return $conditions;
    }
}
