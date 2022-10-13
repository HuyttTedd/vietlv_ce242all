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

namespace Mageplaza\OrderAttributes\Controller\Adminhtml\Attribute;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Swatches\Model\Swatch;
use Mageplaza\OrderAttributes\Controller\Adminhtml\Attribute;
use Zend_Validate_Exception;

/**
 * Class Save
 * @package Mageplaza\OrderAttributes\Controller\Adminhtml\Attribute
 */
class Save extends Attribute
{
    /**
     * @return Redirect|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $this->returnResult('mporderattributes/*/', []);
        }

        if (isset($data['serialized_options']) && $this->helperData->versionCompare('2.2.6')) {
            $data = array_replace_recursive($data, $this->formData->unserialize($data['serialized_options']));
        }

        $attributeObject = $this->_initAttribute();

        $attributeId = $this->getRequest()->getParam('attribute_id');
        if ($attributeId) {
            $attributeObject->load($attributeId);
            $data['frontend_input'] = $attributeObject->getFrontendInput();

            unset($data['attribute_code']);
        } else {
            $data['backend_type'] = $this->helperData->getBackendTypeByInputType($data['frontend_input']);
        }

        $swatches = [];
        $options = [];
        $fields = [
            'labels',
            'tooltips',
            'store_id',
            'customer_group',
            'value_depend',
            'shipping_depend',
            'option',
            'default',
            'optionvisual',
            'defaultvisual',
            'swatchvisual'
        ];
        foreach ($fields as $item) {
            if (empty($data[$item])) {
                continue;
            }

            switch ($item) {
                case 'labels':
                case 'tooltips':
                    $data[$item] = $this->helperData->jsonEncodeData($data[$item]);
                    break;
                case 'store_id':
                case 'customer_group':
                case 'value_depend':
                case 'shipping_depend':
                    $data[$item] = implode(',', $data[$item]);
                    break;
                case 'default':
                case 'defaultvisual':
                    foreach ($data[$item] as $index => $datum) {
                        if (!empty($data[$item]['delete'][$datum])) {
                            unset($data[$item][$index]);
                        }
                    }
                    $options[$item] = $data[$item];
                    break;
                case 'option':
                case 'optionvisual':
                    foreach ($data[$item]['value'] as $index => $datum) {
                        if (!empty($data[$item]['delete'][$index])) {
                            unset($data[$item]['value'][$index]);
                        }
                    }
                    $options[$item] = $data[$item];
                    break;
                case 'swatchvisual':
                    foreach ($data[$item]['value'] as $index => $datum) {
                        $swatches[$index] = [
                            'swatch_value' => $datum,
                            'swatch_type' => $this->determineSwatchType($datum)
                        ];
                    }

                    $data['additional_data'] = $this->helperData->jsonEncodeData($swatches);
                    break;
            }
        }

        if (!empty($options)) {
            $data['options'] = $this->helperData->jsonEncodeData($options);

            unset($data['option']);
            if (isset($data['default'])) {
                unset($data['default']);
            }
        }

        if (!isset($data['shipping_depend'])) {
            $data['shipping_depend'] = null;
        }

        if ($defaultValueField = $this->helperData->getDefaultValueByInput($data['frontend_input'])) {
            $defaultValue = $this->getRequest()->getParam($defaultValueField);
            $data['default_value'] = $defaultValue;

            if ($defaultValueField === 'default_value_date' && $defaultValue) {
                $data['default_value'] = date('Y-m-d', strtotime($defaultValue));
            }
        }

        try {
            $attributeObject->addData($data)->save();
            $this->_eventManager->dispatch(
                'mporderattributes_attribute_save',
                ['attribute' => $attributeObject]
            );

            $this->messageManager->addSuccessMessage(__('The attribute has been saved.'));
            $this->_session->setAttributeData(false);
            if ($this->getRequest()->getParam('back', false)) {
                return $this->returnResult('mporderattributes/*/edit', [
                    'id' => $attributeObject->getId(),
                    '_current' => true
                ]);
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_session->setAttributeData($data);

            if ($attributeId) {
                return $this->returnResult('mporderattributes/*/edit', ['id' => $attributeId, '_current' => true]);
            }
        }

        return $this->returnResult('mporderattributes/*/', []);
    }

    /**
     * @param string $value
     *
     * @return int
     */
    protected function determineSwatchType($value)
    {
        if (strncmp($value, '#', 1) === 0) {
            return Swatch::SWATCH_TYPE_VISUAL_COLOR;
        }

        if (strncmp($value, '/', 1) === 0) {
            return Swatch::SWATCH_TYPE_VISUAL_IMAGE;
        }

        return Swatch::SWATCH_TYPE_EMPTY;
    }
}
