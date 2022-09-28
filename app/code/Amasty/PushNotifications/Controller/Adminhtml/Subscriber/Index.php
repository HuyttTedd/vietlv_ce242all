<?php

namespace Amasty\PushNotifications\Controller\Adminhtml\Subscriber;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Amasty\PushNotifications\Controller\Adminhtml\Subscriber
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_PushNotifications::subscriber_list');
        $resultPage->addBreadcrumb(__('Subscribers'), __('Subscribers'));
        $resultPage->addBreadcrumb(__('Manage Subscribers'), __('Manage Subscribers'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Subscribers'));

        return $resultPage;
    }
}
