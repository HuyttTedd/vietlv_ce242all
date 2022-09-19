<?php

namespace Amasty\PushNotifications\Controller\Adminhtml\Subscriber;

use Magento\Backend\App\Action\Context;
use Amasty\PushNotifications\Api\SubscriberRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

class Delete extends \Amasty\PushNotifications\Controller\Adminhtml\Subscriber
{
    /**
     * @var SubscriberRepositoryInterface
     */
    private $repository;

    public function __construct(
        Context $context,
        SubscriberRepositoryInterface $repository
    ) {
        parent::__construct($context);
        $this->repository = $repository;
    }

    /**
     * Delete action
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        if ($id) {
            try {
                $this->repository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('Subscriber is deleted.'));
            } catch (LocalizedException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Subscriber ID is not found.'));
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}
