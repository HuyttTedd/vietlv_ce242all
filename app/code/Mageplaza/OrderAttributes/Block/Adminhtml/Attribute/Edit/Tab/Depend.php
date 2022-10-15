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
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\CollectionFactory;

/**
 * Class Depend
 * @package Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit\Tab
 */
class Depend extends Generic
{
    /**
     * @var CollectionFactory
     */
    protected $attributeCollection;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param CollectionFactory $attributeCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CollectionFactory $attributeCollection,
        array $data = []
    ) {
        $this->attributeCollection = $attributeCollection;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var Attribute $attributeObject */
        $attributeObject = $this->getAttributeObject();

        /** @var Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset('depend_fieldset', ['legend' => __('Depend Attributes')]);

        $dependFields = [['value' => '', 'label' => __('None')]];
        $dependValues = [];

        /** @var Attribute $attribute */
        foreach ($this->attributeCollection->create() as $attribute) {
            if ($attribute->getId() == $attributeObject->getId()) {
                continue;
            }

            if (in_array($attribute->getFrontendInput(), ['select', 'select_visual', 'boolean'])) {
                $dependFields[] = [
                    'value' => $attribute->getId(),
                    'label' => $attribute->getFrontendLabel()
                ];

                $attrOptions = Data::jsonDecode($attribute->getOptions());

                if (isset($attrOptions['option']['value'])) {
                    $options = $attrOptions['option']['value'];
                } elseif (isset($attrOptions['optionvisual']['value'])) {
                    $options = $attrOptions['optionvisual']['value'];
                } else {
                    $options = [
                        0 => [0 => __('No')],
                        1 => [0 => __('Yes')]
                    ];
                }

                foreach ($options as $index => $option) {
                    $dependValues[] = [
                        'value' => $attribute->getId() . '_' . $index,
                        'label' => __($option[0])
                    ];
                }
            }
        }

        $fieldset->addField('field_depend', 'select', [
            'name' => 'field_depend',
            'label' => __('Select a parent field'),
            'title' => __('Select a parent field'),
            'values' => $dependFields
        ]);

        $fieldset->addField('value_depend', 'multiselect', [
            'name' => 'value_depend',
            'label' => __('Depend on options'),
            'title' => __('Depend on options'),
            'values' => $dependValues,
            'note' => __('Select the following options to show  this current attribute')
        ])->setSize(5);

        $fieldset->addField(
            'shipping_depend',
            'Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit\Tab\Renderer\ShippingMethod',
            [
                'name' => 'field_depend',
                'label' => __('Depend on shipping methods'),
                'title' => __('Depend on shipping methods'),
                'note' => __('This attribute will be shown depending on selected shipping method(s).<br/>Hold down ctrl key while clicking to deselect an option.')
            ]
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @inheritdoc
     */
    protected function _initFormValues()
    {
        $this->getForm()->addValues($this->getAttributeObject()->getData());

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
