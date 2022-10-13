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
use Mageplaza\OrderAttributes\Controller\Adminhtml\Attribute;

/**
 * Class Delete
 * @package Mageplaza\OrderAttributes\Controller\Adminhtml\Attribute
 */
class Delete extends Attribute
{
    /**
     * @return Redirect
     */
    public function execute()
    {
        $attributeId = $this->getRequest()->getParam('id');
        if ($attributeId) {
            $attributeObject = $this->_initAttribute()->load($attributeId);
            try {
                $attributeObject->delete();

                $this->_eventManager->dispatch(
                    'mporderattributes_attribute_delete',
                    ['attribute' => $attributeObject]
                );

                $this->messageManager->addSuccessMessage(__('The attribute has been deleted.'));

                return $this->returnResult('mporderattributes/*/', []);
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('We can\'t delete the attribute right now.')
                );

                return $this->returnResult('mporderattributes/*/edit', ['id' => $attributeId, '_current' => true]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find an attribute to delete.'));

        return $this->returnResult('mporderattributes/*/', []);
    }
}
