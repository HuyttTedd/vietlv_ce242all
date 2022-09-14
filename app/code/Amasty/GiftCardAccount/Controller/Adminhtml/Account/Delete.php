<?php
declare(strict_types=1);

namespace Amasty\GiftCardAccount\Controller\Adminhtml\Account;

use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Controller\Adminhtml\AbstractAccount;
use Amasty\GiftCardAccount\Model\GiftCardAccount\Repository;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Delete extends AbstractAccount
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Action\Context $context,
        Repository $repository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->logger = $logger;
    }

    public function execute()
    {
        if ($id = (int)$this->getRequest()->getParam(GiftCardAccountInterface::ACCOUNT_ID)) {
            try {
                $this->repository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The account has been deleted.'));

                return $this->resultRedirectFactory->create()->setPath('amgcard/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addErrorMessage(
                    __('Can\'t delete account right now. Please review the log and try again.')
                );
            }

            return $this->resultRedirectFactory->create()->setPath(
                'amgcard/*/edit',
                [GiftCardAccountInterface::ACCOUNT_ID => $id]
            );
        } else {
            $this->messageManager->addErrorMessage(__('Can\'t find a account to delete.'));
        }

        return $this->resultRedirectFactory->create()->setPath('amgcard/*/');
    }
}
