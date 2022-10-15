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

namespace Mageplaza\OrderAttributes\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session\Quote;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Sales\Block\Adminhtml\Order\Create\Form\AbstractForm;
use Magento\Sales\Model\AdminOrder\Create;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\CollectionFactory;

/**
 * Class SalesOrderCreate
 * @package Mageplaza\OrderAttributes\Block\Adminhtml
 */
class SalesOrderCreate extends AbstractForm
{
    /**
     * @var array
     */
    protected $_attributes = [];

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Additional constructor.
     *
     * @param Context $context
     * @param Quote $sessionQuote
     * @param Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param FormFactory $formFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param Data $dataHelper
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Quote $sessionQuote,
        Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        FormFactory $formFactory,
        DataObjectProcessor $dataObjectProcessor,
        Data $dataHelper,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->collectionFactory = $collectionFactory;

        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $formFactory,
            $dataObjectProcessor,
            $data
        );
    }

    /**
     * Prepare Form and add elements to form
     *
     * @return $this|AbstractForm
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _prepareForm()
    {
        $fieldset = $this->_form->addFieldset('additional_fieldset', ['legend' => '', 'collapsable' => false]);
        $storeId = $this->getStoreId() ?: 0;

        foreach ($this->getAttributes() as $attribute) {
            $tooltips = Data::jsonDecode($attribute->getTooltips());
            $tooltip = !empty($tooltips[$storeId]) ? $tooltips[$storeId] : null;
            $default = $attribute->getDefaultValue();
            $options = [];

            $frontendInput = $attribute->getFrontendInput();
            switch ($frontendInput) {
                case 'boolean':
                    $options = [
                        ['value' => '', 'label' => __('Please select an option')],
                        ['value' => '0', 'label' => __('No')],
                        ['value' => '1', 'label' => __('Yes')]
                    ];
                    break;
                case 'select':
                    array_unshift($options, ['value' => '', 'label' => __('Please select an option')]);
                case 'multiselect':
                    $attrOptions = Data::jsonDecode($attribute->getOptions());
                    foreach ($attrOptions['option']['value'] as $index => $item) {
                        $optionLabel = empty($item[$storeId]) ? $item[0] : $item[$storeId];
                        $options[] = [
                            'value' => $index,
                            'label' => __($optionLabel)
                        ];
                    }
                    if (isset($attrOptions['default'])) {
                        $default = implode(',', $attrOptions['default']);
                    }
                    break;
                case 'select_visual':
                    array_unshift($options, ['value' => '', 'label' => __('Please select an option')]);
                case 'multiselect_visual':
                    $attrOptions = Data::jsonDecode($attribute->getOptions());
                    foreach ($attrOptions['optionvisual']['value'] as $index => $item) {
                        $optionLabel = empty($item[$storeId]) ? $item[0] : $item[$storeId];
                        $options[] = [
                            'value' => $index,
                            'label' => __($optionLabel),
                        ];
                    }
                    if (isset($attrOptions['defaultvisual'])) {
                        $default = implode(',', $attrOptions['defaultvisual']);
                    }
                    break;
                case 'date':
                    $default = $default ? date('m/d/Y', strtotime($default)) : null;
                    break;
                default:
                    $options = [];
                    break;
            }

            $type = $this->dataHelper->getFieldTypeByInputType($frontendInput);
            $field = $fieldset->addField(
                'mpOrderAttributes-' . $attribute->getAttributeCode(),
                $type === 'content' ? 'textarea' : $type,
                [
                    'name' => 'mpOrderAttributes[' . $attribute->getAttributeCode() . ']',
                    'label' => $attribute->getFrontendLabel(),
                    'title' => $attribute->getFrontendLabel(),
                    'class' => $attribute->getFrontendClass(),
                    'required' => $attribute->getIsRequired(),
                    'values' => $options,
                    'value' => $this->getQuote()->getData($attribute->getAttributeCode()) ?: $default,
                    'date_format' => $this->_localeDate->getDateFormatWithLongYear()
                ]
            )->setSize(5);

            if ($attribute->getUseTooltip() && $tooltip) {
                $tooltip = '<div class="admin__field-tooltip tooltip">
                                <span class="admin__field-tooltip-action action-help"></span>
                                <span class="admin__field-tooltip-content">' . $tooltip . '</span >
                            </div>';
                $field->setAfterElementHtml($tooltip);
            }
        }

        $this->setForm($this->_form);

        return $this;
    }

    /**
     * @param null $position
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getAttributes($position = null)
    {
        if (!$this->dataHelper->isEnabled($this->getStoreId())) {
            return [];
        }

        if (count($this->_attributes)) {
            return $this->_attributes;
        }

        $attributes = $this->collectionFactory->create()->addFieldToFilter('position', ['in' => $position]);
        foreach ($attributes as $attribute) {
            if ($this->dataHelper->isVisible(
                $attribute,
                $this->getStoreId(),
                $this->getQuote()->getCustomerGroupId()
            )
            ) {
                $this->_attributes[] = $attribute;
            }
        }

        return $this->_attributes;
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getConfig()
    {
        $data = [
            'loadBaseUrl' => $this->_urlBuilder->getUrl('mporderattributes/salesordercreate/index'),
            'selectedShippingMethod' => $this->_orderCreate->getShippingAddress()->getShippingMethod(),
            'attributes' => [],
            'attributeDepend' => [],
            'shippingDepend' => [],
            'contentType' => [],
            'tinymceConfig' => $this->dataHelper->getTinymceConfig()
        ];

        foreach ($this->dataHelper->getFilteredAttributes(
            $this->getStoreId(),
            $this->getQuote()->getCustomerGroupId()
        ) as $attribute) {
            $data['attributes'][] = $attribute->getData();
            $frontendInput = $attribute->getFrontendInput();

            if ($attribute->getFieldDepend() || in_array($frontendInput, ['select', 'select_visual', 'boolean'])) {
                $data['attributeDepend'][] = $attribute->getData();
            }

            if ($attribute->getShippingDepend()) {
                $data['shippingDepend'][] = $attribute->getData();
            }

            if ($frontendInput === 'textarea_visual') {
                $data['contentType'][] = $attribute->getData();
            }
        }

        return $data;
    }
}
