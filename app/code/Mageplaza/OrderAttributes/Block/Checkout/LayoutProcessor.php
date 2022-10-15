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

namespace Mageplaza\OrderAttributes\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Swatches\Helper\Media;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\Config\Source\Position;

/**
 * Class LayoutProcessor
 * @package Mageplaza\OrderAttributes\Block\Checkout
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var Media
     */
    protected $swatchHelper;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Data $helperData
     * @param Media $swatchHelper
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Data $helperData,
        Media $swatchHelper,
        Session $customerSession,
        StoreManagerInterface $storeManager
    ) {
        $this->helperData = $helperData;
        $this->swatchHelper = $swatchHelper;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     *
     * @return array
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function process($jsLayout)
    {
        if (!$this->helperData->isEnabled()) {
            return $jsLayout;
        }

        $attributes = $this->helperData->getFilteredAttributes();

        $customerHasAddress = false;
        if ($customer = $this->customerSession->getCustomer()) {
            $customerHasAddress = (count($customer->getAddresses()) > 0);
        }

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            $additionalClass = '';
            $attributeCode = $attribute->getAttributeCode();

            switch ($attribute->getPosition()) {
                case Position::ADDRESS:
                    if (!$customerHasAddress) {
                        $customScope = 'mpShippingAddressAttributes';
                        $fieldset = &$jsLayout['components']['checkout']['children']['steps']['children']
                        ['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']
                        ['children']['mpOrderAttributes']['children'][$attributeCode];
                    } else {
                        $customScope = 'mpShippingAddressNewAttributes';
                        $fieldset = &$jsLayout['components']['checkout']['children']['steps']['children']
                        ['shipping-step']['children']['shippingAddress']['children']['before-form']['children']
                        ['mpOrderAttributes']['children'][$attributeCode];
                    }
                    if ($this->helperData->isOscPage()) {
                        $additionalClass = 'col-mp';
                    }
                    break;
                case Position::SHIPPING_TOP:
                    $customScope = 'mpShippingMethodTopAttributes';
                    $fieldset = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                    ['children']['shippingAddress']['children']['before-shipping-method-form']['children']
                    ['mpOrderAttributes']['children'][$attributeCode];
                    break;
                case Position::SHIPPING_BOTTOM:
                    $customScope = 'mpShippingMethodBottomAttributes';
                    $fieldset = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                    ['children']['shippingAddress']['children']['mpOrderAttributes']['children'][$attributeCode];
                    break;
                case Position::PAYMENT_TOP:
                    $customScope = 'mpPaymentMethodTopAttributes';
                    $fieldset = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                    ['children']['payment']['children']['beforeMethods']['children']['mpOrderAttributes']
                    ['children'][$attributeCode];
                    break;
                case Position::PAYMENT_BOTTOM:
                    $customScope = 'mpPaymentMethodBottomAttributes';
                    $fieldset = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                    ['children']['payment']['children']['afterMethods']['children']['mpOrderAttributes']
                    ['children'][$attributeCode];
                    break;
                //                case Position::ORDER_SUMMARY:
                default:
                    if ($this->helperData->isOscPage()) {
                        $customScope = 'mpOrderSummaryOscAttributes';
                        $fieldset = &$jsLayout['components']['checkout']['children']['sidebar']['children']
                        ['place-order-information-left']['children']['addition-information']['children']
                        ['mpOrderAttributes']['children'][$attributeCode];
                    } else {
                        $customScope = 'mpOrderSummaryAttributes';
                        $fieldset = &$jsLayout['components']['checkout']['children']['sidebar']['children']['summary']
                        ['children']['itemsAfter']['children']['mpOrderAttributes']['children'][$attributeCode];
                    }
                    break;
            }

            $fieldset = $this->getAttributeField($attribute, $customScope, $additionalClass);
        }

        return $jsLayout;
    }

    /**
     * Convert attribute to field
     *
     * @param Attribute $attribute
     * @param string $customScope
     * @param string $additionalClass
     *
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getAttributeField($attribute, $customScope, $additionalClass)
    {
        $frontendInput = $attribute->getFrontendInput();
        $attributeCode = $attribute->getAttributeCode();
        $component = $this->helperData->getComponentByInputType($frontendInput);
        $elementTmpl = $this->helperData->getElementTmplByInputType($frontendInput);
        $fieldType = $this->helperData->getFieldTypeByInputType($frontendInput);

        $storeId = $this->storeManager->getStore()->getId();
        $label = $this->helperData->prepareLabel($attribute, $storeId);

        $tooltips = $this->helperData->jsonDecodeData($attribute->getTooltips());
        $tooltip = !empty($tooltips[$storeId]) ? $tooltips[$storeId] : null;

        $validation = [];
        if ($attribute->getIsRequired()) {
            $additionalClass .= ' required';
            $validation['required-entry'] = true;
        }
        if ($attribute->getFrontendClass()) {
            $validation[$attribute->getFrontendClass()] = true;
        }

        $options = [];
        $default = $attribute->getDefaultValue();
        switch ($frontendInput) {
            case 'boolean':
                $options = [
                    ['value' => '0', 'label' => __('No')],
                    ['value' => '1', 'label' => __('Yes')]
                ];
                break;
            case 'select':
            case 'multiselect':
                $attrOptions = $this->helperData->jsonDecodeData($attribute->getOptions());
                if (!empty($attrOptions['option']['value'])) {
                    foreach ($attrOptions['option']['value'] as $index => $item) {
                        $optionLabel = empty($item[$storeId]) ? $item[0] : $item[$storeId];
                        $options[] = [
                            'value' => $index,
                            'label' => __($optionLabel)
                        ];
                    }
                }
                if (isset($attrOptions['default'])) {
                    $default = implode(',', $attrOptions['default']);
                }
                break;
            case 'select_visual':
            case 'multiselect_visual':
                $attrOptions = $this->helperData->jsonDecodeData($attribute->getOptions());
                if (!empty($attrOptions['optionvisual']['value'])) {
                    foreach ($attrOptions['optionvisual']['value'] as $index => $item) {
                        $swatchData = $this->helperData->jsonDecodeData($attribute->getAdditionalData());
                        $optionLabel = empty($item[$storeId]) ? $item[0] : $item[$storeId];
                        $options[] = [
                            'value' => $index,
                            'label' => __($optionLabel),
                            'visual' => $this->reformatSwatchLabels($swatchData[$index]['swatch_value'])
                        ];
                    }
                }
                if (isset($attrOptions['defaultvisual'])) {
                    $default = implode(',', $attrOptions['defaultvisual']);
                }
                break;
            case 'date':
                $default = $default ? date('m/d/Y', strtotime($default)) : null;
                $additionalClass .= ' date';
                $options = [
                    'changeMonth' => true,
                    'changeYear' => true,
                    'showOn' => 'both',
                ];
                break;
        }

        $name = $attributeCode;
        if (strpos($frontendInput, 'multiselect') !== false) {
            $name .= '[]';
        }

        $field = [
            'component' => $component,
            'fieldType' => $fieldType,
            'dataScope' => $customScope . '.' . $name,
            'label' => $label,
            'options' => $options,
            'caption' => __('Please select an option'),
            'provider' => 'mpOrderAttributesCheckoutProvider',
            'visible' => true,
            'validation' => $validation,
            'sortOrder' => $attribute->getSortOrder(),
            'default' => $default,
            'config' => [
                'rows' => 5,
                'additionalClasses' => $additionalClass,
                'customScope' => $customScope,
                'elementTmpl' => $elementTmpl,
                'template' => 'ui/form/field',
            ],
        ];

        if ($tooltip && $attribute->getUseTooltip()) {
            $field['config']['tooltip'] = [
                'description' => $tooltip
            ];
        }

        return $field;
    }

    /**
     * Parse swatch labels for template
     *
     * @param $swatchValue
     *
     * @return string
     */
    protected function reformatSwatchLabels($swatchValue)
    {
        if (strncmp($swatchValue, '#', 1) === 0) {
            return '<div class="color" style="background-color: ' . $swatchValue . '"></div>';
        }

        if (strncmp($swatchValue, '/', 1) === 0) {
            return '<img class="image" src="' . $this->swatchHelper->getSwatchAttributeImage(
                'swatch_thumb',
                $swatchValue
            ) . '">';
        }

        return '';
    }
}
