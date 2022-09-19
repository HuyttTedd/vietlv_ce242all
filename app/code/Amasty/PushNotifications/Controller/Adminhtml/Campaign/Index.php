<?php

namespace Amasty\PushNotifications\Controller\Adminhtml\Campaign;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Amasty\PushNotifications\Controller\Adminhtml\Campaign
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_PushNotifications::campaign_list');
        $resultPage->addBreadcrumb(__('Campaigns'), __('Campaigns'));
        $resultPage->addBreadcrumb(__('Manage Campaigns'), __('Manage Campaigns'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Campaigns'));

        return $resultPage;
    }
}
