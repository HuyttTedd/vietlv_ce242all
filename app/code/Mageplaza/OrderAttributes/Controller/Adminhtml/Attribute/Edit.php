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

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\Result\Redirect;
use Mageplaza\OrderAttributes\Controller\Adminhtml\Attribute;

/**
 * Class Edit
 * @package Mageplaza\OrderAttributes\Controller\Adminhtml\Attribute
 */
class Edit extends Attribute
{
    /**
     * @return Page|Redirect
     */
    public function execute()
    {
        $attributeObject = $this->_initAttribute();

        $attributeId = $this->getRequest()->getParam('id');
        if ($attributeId) {
            $attributeObject->load($attributeId);

            if (!$attributeObject->getId()) {
                $this->messageManager->addErrorMessage(__('This attribute no longer exists.'));

                return $this->returnResult('mporderattributes/*/', []);
            }
        }

        // restore attribute data
        $data = $this->_session->getAttributeData(true);
        if (!empty($data)) {
            $attributeObject->addData($data);
        }

        $this->_coreRegistry->register('entity_attribute', $attributeObject);

        $pageTitle = $attributeId ? $attributeObject->getFrontendLabel() : __('New Order Attribute');

        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);

        return $resultPage;
    }
}
