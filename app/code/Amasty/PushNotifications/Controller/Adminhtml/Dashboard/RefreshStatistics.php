<?php

namespace Amasty\PushNotifications\Controller\Adminhtml\Dashboard;

use Amasty\PushNotifications\Controller\Adminhtml\Dashboard;
use Magento\Backend\App\Action\Context;
use Psr\Log\LoggerInterface;

class RefreshStatistics extends \Amasty\PushNotifications\Controller\Adminhtml\Dashboard
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $this->messageManager->addSuccessMessage(__('We updated lifetime statistic.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We can\'t refresh lifetime statistics.'));
            $this->logger->critical($e);
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*');
    }
}
