<?php

namespace Amasty\PushNotifications\Controller\Adminhtml\Campaign;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Amasty\PushNotifications\Api\CampaignRepositoryInterface;
use Amasty\PushNotifications\Model\CampaignFactory;

class Edit extends \Amasty\PushNotifications\Controller\Adminhtml\Campaign
{
    /**
     * @var CampaignRepositoryInterface
     */
    private $repository;

    /**
     * @var CampaignFactory
     */
    private $campaignFactory;

    public function __construct(
        Context $context,
        CampaignRepositoryInterface $repository,
        CampaignFactory $campaignFactory
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->campaignFactory = $campaignFactory;
    }

    /**
     * Edit action
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        if ($id) {
            try {
                $model = $this->repository->getById($id);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This campaign no longer exists.'));

                return $this->_redirect('*/*/index');
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_PushNotifications::campaign');
        $resultPage->addBreadcrumb(__('Campaign'), __('Campaign'));
        $resultPage->getConfig()->getTitle()->prepend(
            isset($model) && $model->getId() ? $model->getName() : __('New Campaign')
        );

        return $resultPage;
    }
}
