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

namespace Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Config\Model\Config\Source\YesnoFactory;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\Config\Source\PositionFactory;

/**
 * Class Front
 * @package Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit\Tab
 */
class Front extends Generic
{
    /**
     * @var YesnoFactory
     */
    protected $yesnoFactory;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var Store
     */
    protected $systemStore;

    /**
     * @var PositionFactory
     */
    protected $positionFactory;

    /**
     * Send constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param YesnoFactory $yesnoFactory
     * @param Data $dataHelper
     * @param CollectionFactory $groupCollectionFactory
     * @param Store $systemStore
     * @param PositionFactory $positionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        YesnoFactory $yesnoFactory,
        Data $dataHelper,
        CollectionFactory $groupCollectionFactory,
        Store $systemStore,
        PositionFactory $positionFactory,
        array $data = []
    ) {
        $this->yesnoFactory = $yesnoFactory;
        $this->dataHelper = $dataHelper;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->systemStore = $systemStore;
        $this->positionFactory = $positionFactory;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset('front_fieldset', ['legend' => __('Frontend Properties')]);

        $yesno = $this->yesnoFactory->create()->toOptionArray();

        $fieldset->addField('position', 'select', [
            'name' => 'position',
            'label' => __('Position'),
            'title' => __('Position'),
            'values' => $this->positionFactory->create()->toOptionArray(),
            'note' => __(
                'Compatible with <a href="%1" target="_blank">One Step Checkout</a>',
                'https://www.mageplaza.com/magento-2-one-step-checkout-extension?utm_source=extension&utm_medium=text&utm_campaign=order-attribute'
            )
        ]);

        $fieldset->addField('use_tooltip', 'select', [
            'name' => 'use_tooltip',
            'label' => __('Use Tooltip'),
            'title' => __('Use Tooltip'),
            'values' => $yesno,
            'note' => __('Enter tooltips in Manage Labels / Options tab')
        ]);

        /** @var RendererInterface $rendererBlock */
        $rendererBlock = $this->getLayout()->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
        $fieldset->addField('store_id', 'multiselect', [
            'name' => 'store_id',
            'label' => __('Store View'),
            'title' => __('Store View'),
            'values' => $this->systemStore->getStoreValuesForForm(false, true),
            'value' => 0,
            'required' => true
        ])->setRenderer($rendererBlock)->setSize(5);

        $fieldset->addField('customer_group', 'multiselect', [
            'name' => 'customer_group',
            'label' => __('Customer Group'),
            'title' => __('Customer Group'),
            'values' => $this->groupCollectionFactory->create()->toOptionArray(),
            'value' => 0,
            'required' => true
        ])->setSize(5);

        $fieldset->addField('show_in_frontend_order', 'select', [
            'name' => 'show_in_frontend_order',
            'label' => __('Add to Sales Order View'),
            'title' => __('Add to Sales Order View'),
            'values' => $yesno,
            'note' => __('Select <b>Yes</b> to add this attribute to the Order View Page (include PDF printout).')
        ]);

        $fieldset->addField('sort_order', 'text', [
            'name' => 'sort_order',
            'label' => __('Sort Order'),
            'title' => __('Sort Order'),
            'class' => 'validate-digits',
            'note' => __('The priority of attributes display. The smallest (0) is the top priority.')
        ]);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @inheritdoc
     */
    protected function _initFormValues()
    {
        /** @var Attribute $attributeObject */
        $attributeObject = $this->getAttributeObject();

        if ($attributeObject->getData('store_id') === null) {
            $attributeObject->setData('store_id', 0);
        }

        if ($attributeObject->getData('customer_group') === null) {
            $group = array_keys($this->groupCollectionFactory->create()->toOptionArray());
            $attributeObject->setData('customer_group', implode(',', $group));
        }

        $this->getForm()->addValues($attributeObject->getData());

        return parent::_initFormValues();
    }

    /**
     * @return mixed
     */
    protected function getAttributeObject()
    {
        return $this->_coreRegistry->registry('entity_attribute');
    }
}
