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

use Magento\Framework\DataObject;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\OrderAttributes\Controller\Adminhtml\Attribute;

/**
 * Class Validate
 * @package Mageplaza\OrderAttributes\Controller\Adminhtml\Attribute
 */
class Validate extends Attribute
{
    const VALIDATION_RULE_PATTERN = '/^[a-z][a-z_0-9]{0,59}$/';

    /**
     * @return void
     * @throws LocalizedException
     */
    public function execute()
    {
        $response = new DataObject();
        $response->setError(false);

        $request = $this->getRequest();

        $attributeCode = trim($request->getParam('attribute_code'));
        $attributeId = $request->getParam('attribute_id');

        try {
            $attributeObject = $this->_initAttribute();
            if ($attributeId) {
                $attributeObject->load($attributeId);
                if (!$attributeObject->getId()) {
                    throw new InputException(__('The Attribute with the "%1" ID doesn\'t exist.', $attributeId));
                }
            } else {
                $this->validateAttributeCode($attributeObject, $attributeCode);
            }
            $frontendLabel = trim($request->getParam('frontend_label'));
            if (!$frontendLabel) {
                throw new InputException(__('Default label is required.'));
            }

            $frontendInput = $request->getParam('frontend_input') ?: $attributeObject->getFrontendInput();

            if (in_array($frontendInput, ['select_visual', 'multiselect_visual'])) {
                $this->validateOptionVisual();
            } else {
                $this->validateOptions();
            }
        } catch (InputException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->_view->getLayout()->initMessages();
            $response->setData([
                'error' => true,
                'html_message' => $this->_view->getLayout()->getMessagesBlock()->getGroupedHtml()
            ]);
        }

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * @param string $attributeCode
     * @param \Mageplaza\OrderAttributes\Model\Attribute $attributeObject
     *
     * @throws InputException
     * @throws LocalizedException
     */
    public function validateAttributeCode($attributeObject, $attributeCode)
    {
        if (!$attributeCode) {
            throw new InputException(__('Attribute Code is required.'));
        }

        if (!preg_match(self::VALIDATION_RULE_PATTERN, $attributeCode)) {
            $message = __(
                'Attribute code "%1" is invalid. Please use only letters (a-z), numbers (0-9)' .
                'or underscore(_) in this field, first character should be a letter.',
                $attributeCode
            );

            throw new InputException($message);
        }
        $attributeObject->loadByCode($attributeCode);
        if ($attributeObject->getId()) {
            throw new InputException(__('An attribute with this code already exists.'));
        }

        if ($attributeObject->isColumnExists($attributeCode)) {
            throw new InputException(__('An attribute with this code already exists in sales order.'));
        }
    }

    /**
     * @throws InputException
     */
    public function validateOptionVisual()
    {
        $option = $this->getRequest()->getParam('optionvisual', []);

        $this->validateOptionValue($option);
    }

    /**
     * @param array $option
     *
     * @throws InputException
     */
    public function validateOptionValue($option)
    {
        if (!empty($option['value'])) {
            foreach ($option['value'] as $key => $value) {
                if (empty($option['delete'][$key]) && isset($value[0]) && $value[0] === '') {
                    throw new InputException(__('The value of Admin scope can\'t be empty.'));
                }
            }
        }
    }

    /**
     * Check that admin does not try to create option with empty admin scope option.
     * @throws InputException
     */
    public function validateOptions()
    {
        $serializedOptions = $this->getRequest()->getParam('serialized_options', []);
        if ($serializedOptions && $this->helperData->versionCompare('2.2.6')) {
            $options = $this->formData->unserialize($serializedOptions);
            foreach ($options as $option) {
                $this->validateOptionValue($option);
            }
        }
    }
}
