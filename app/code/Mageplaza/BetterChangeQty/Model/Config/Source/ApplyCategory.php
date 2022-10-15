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
 * @package     Mageplaza_BetterChangeQty
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BetterChangeQty\Model\Config\Source;

use Magento\Catalog\Block\Adminhtml\Category\Tree;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ApplyCategory
 *
 * @package Mageplaza\BetterChangeQty\Model\Config\Source
 */
class ApplyCategory implements OptionSourceInterface
{
    /**
     * @var Tree
     */
    protected $categoryTree;

    /**
     * ApplyCategory constructor.
     *
     * @param Tree $categoryTree
     */
    public function __construct(Tree $categoryTree)
    {
        $this->categoryTree = $categoryTree;
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionArray()
    {
        $options = [0 => __('All Categories')];

        $this->getTree($this->categoryTree->getTree(), $options);

        return $options;
    }

    /**
     * @param $categories
     * @param $options
     */
    public function getTree($categories, &$options)
    {
        foreach ($categories as $category) {
            $indent = '';

            if ($level = substr_count($category['path'], '/') - 1) {
                $indent = str_repeat('- - ', $level);
            }

            $options[$category['id']] = $indent . htmlspecialchars_decode($category['text'], ENT_QUOTES);

            if (!empty($category['children'])) {
                $this->getTree($category['children'], $options);
            }
        }
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function toOptionArray()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }
}
