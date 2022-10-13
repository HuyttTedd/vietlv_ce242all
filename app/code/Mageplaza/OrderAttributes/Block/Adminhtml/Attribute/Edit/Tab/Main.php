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
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\Config\Source\FrontendClassFactory;
use Mageplaza\OrderAttributes\Model\Config\Source\InputFilterFactory;
use Mageplaza\OrderAttributes\Model\Config\Source\InputTypeFactory;

/**
 * Class Main
 * @package Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit\Tab
 */
class Main extends Generic
{
    /**
     * Attribute instance
     *
     * @var Attribute
     */
    protected $_attribute = null;

    /**
     * @var YesnoFactory
     */
    protected $yesnoFactory;

    /**
     * @var InputTypeFactory
     */
    protected $inputTypeFactory;

    /**
     * @var FrontendClassFactory
     */
    protected $frontendClassFactory;

    /**
     * @var InputFilterFactory
     */
    protected $inputFilterFactory;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param YesnoFactory $yesnoFactory
     * @param InputTypeFactory $inputTypeFactory
     * @param FrontendClassFactory $frontendClassFactory
     * @param InputFilterFactory $inputFilterFactory
     * @param Data $dataHelper
     * @param array $data
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        YesnoFactory $yesnoFactory,
        InputTypeFactory $inputTypeFactory,
        FrontendClassFactory $frontendClassFactory,
        InputFilterFactory $inputFilterFactory,
        Data $dataHelper,
        array $data = []
    ) {
        $this->yesnoFactory = $yesnoFactory;
        $this->inputTypeFactory = $inputTypeFactory;
        $this->frontendClassFactory = $frontendClassFactory;
        $this->inputFilterFactory = $inputFilterFactory;
        $this->dataHelper = $dataHelper;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        $attributeObject = $this->getAttributeObject();

        /** @var Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset('main_fieldset', ['legend' => __('Properties')]);

        if ($attributeObject->getAttributeId()) {
            $fieldset->addField('attribute_id', 'hidden', ['name' => 'attribute_id']);
        }

        $yesno = $this->yesnoFactory->create()->toOptionArray();

        $fieldset->addField('frontend_label', 'text', [
            'name' => 'frontend_label',
            'label' => __('Default Label'),
            'title' => __('Default label'),
            'required' => true
        ]);

        $validateClass = sprintf(
            'validate-code validate-length maximum-length-%d',
            \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH
        );
        $fieldset->addField('attribute_code', 'text', [
            'name' => 'attribute_code',
            'label' => __('Attribute Code'),
            'title' => __('Attribute Code'),
            'note' => __(
                'For internal use. Must be unique with no spaces. Maximum length of attribute code must be less than %1 symbols.',
                \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH
            ),
            'class' => $validateClass,
            'required' => true
        ]);

        $fieldset->addField('frontend_input', 'select', [
            'name' => 'frontend_input',
            'label' => __('Input Type'),
            'title' => __('Input Type'),
            'value' => 'text',
            'values' => $this->inputTypeFactory->create()->toOptionArray()
        ]);
        $fieldset->addField('max_file_size', 'text', [
            'name' => 'max_file_size',
            'label' => __('Maximum File Size'),
            'title' => __('Maximum File Size'),
            'class' => 'validate-digits',
            'note' => __('File size in bytes.')
        ]);
        $fieldset->addField('allow_extensions', 'text', [
            'name' => 'allow_extensions',
            'label' => __('Allow File Formats'),
            'title' => __('Allow File Formats'),
            'note' => __('Separated by comma(,). Example pdf,doc,zip,jpg,png,gif,jpeg')
        ]);

        $fieldset->addField('default_value_text', 'text', [
            'name' => 'default_value_text',
            'label' => __('Default Value'),
            'title' => __('Default Value'),
            'value' => $attributeObject->getDefaultValue()
        ]);

        $fieldset->addField('default_value_textarea', 'textarea', [
            'name' => 'default_value_textarea',
            'label' => __('Default Value'),
            'title' => __('Default Value'),
            'value' => $attributeObject->getDefaultValue()
        ]);

        $fieldset->addField('default_value_date', 'date', [
            'name' => 'default_value_date',
            'label' => __('Default Value'),
            'title' => __('Default Value'),
            'value' => $attributeObject->getDefaultValue(),
            'date_format' => $this->_localeDate->getDateFormatWithLongYear()
        ]);

        $fieldset->addField('default_value_yesno', 'select', [
            'name' => 'default_value_yesno',
            'label' => __('Default Value'),
            'title' => __('Default Value'),
            'values' => $yesno,
            'value' => $attributeObject->getDefaultValue()
        ]);

        $fieldset->addField('default_value_content', 'textarea', [
            'name' => 'default_value_content',
            'label' => __('Default Value'),
            'title' => __('Default Value'),
            'value' => $attributeObject->getDefaultValue()
        ]);

        $fieldset->addField('is_required', 'select', [
            'name' => 'is_required',
            'label' => __('Values Required'),
            'title' => __('Values Required'),
            'values' => $yesno
        ]);

        $fieldset->addField('frontend_class', 'select', [
            'name' => 'frontend_class',
            'label' => __('Input Validation'),
            'title' => __('Input Validation'),
            'values' => $this->frontendClassFactory->create()->toOptionArray()
        ]);

        $fieldset->addField('input_filter', 'select', [
            'name' => 'input_filter',
            'label' => __('Input/Output Filter'),
            'title' => __('Input/Output Filter'),
            'values' => $this->inputFilterFactory->create()->toOptionArray()
        ]);

        $fieldset->addField('is_used_in_grid', 'select', [
            'name' => 'is_used_in_grid',
            'label' => __('Add to Sales Order Grid'),
            'title' => __('Add to Sales Order Grid'),
            'values' => $yesno,
            'note' => __('Column(s) will be added to column options, filter options & search options of Sales Order Grid.')
        ]);

        $fieldset->addField('add_to_pdf_files', 'link', [
            'name' => 'add_to_pdf_files',
            'label' => __('Add to PDF Files'),
            'title' => __('Add to PDF Files'),
            'href' => 'https://www.mageplaza.com/kb/how-add-order-attributes-to-pdf-order-template.html',
            'value' => __('How to add to PDF Invoice'),
            'target' => '_blank'
        ]);

        $fieldset->addField('add_to_confirm_email', 'link', [
            'name' => 'add_to_confirm_email',
            'label' => __('Add to Confirmation Emails'),
            'title' => __('Add to Confirmation Emails'),
            'href' => 'https://www.mageplaza.com/kb/how-insert-order-attributes-to-transactional-emails.html',
            'value' => __('How to insert to Transactional Emails'),
            'target' => '_blank'
        ]);

        if ($attributeObject->getId()) {
            $form->getElement('attribute_code')->setDisabled(1);
            $form->getElement('frontend_input')->setDisabled(1);
        }

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
     * Return attribute object
     *
     * @return Attribute
     */
    protected function getAttributeObject()
    {
        if (null === $this->_attribute) {
            return $this->_coreRegistry->registry('entity_attribute');
        }

        return $this->_attribute;
    }
}
