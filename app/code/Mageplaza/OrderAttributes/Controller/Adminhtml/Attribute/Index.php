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
use Mageplaza\OrderAttributes\Controller\Adminhtml\Attribute;

/**
 * Class Index
 * @package Mageplaza\OrderAttributes\Controller\Adminhtml\Attribute
 */
class Index extends Attribute
{
    /**
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Attributes'));

        return $resultPage;
    }
}
