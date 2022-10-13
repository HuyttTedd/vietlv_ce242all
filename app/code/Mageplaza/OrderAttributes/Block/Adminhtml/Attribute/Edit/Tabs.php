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
 * @package     Mageplaza_OrderAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit;

/**
 * Class Tabs
 * @package Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Attribute Information'));
    }

    /**
     * @inheritdoc
     */
    protected function _beforeToHtml()
    {
        $this->addTab('main', [
            'label' => __('Properties'),
            'title' => __('Properties'),
            'content' => $this->getChildHtml('main'),
            'active' => true
        ]);
        $this->addTab('front', [
            'label' => __('Frontend Properties'),
            'title' => __('Frontend Properties'),
            'content' => $this->getChildHtml('front')
        ]);
        $this->addTab('manage', [
            'label' => __('Manage Labels/Options'),
            'title' => __('Manage Labels/Options'),
            'content' => $this->getChildHtml('manage')
        ]);
        $this->addTab('depend', [
            'label' => __('Depend Attributes'),
            'title' => __('Depend Attributes'),
            'content' => $this->getChildHtml('depend')
        ]);

        return parent::_beforeToHtml();
    }
}
