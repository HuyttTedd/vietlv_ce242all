<?php

namespace Amasty\PushNotifications\Controller\Adminhtml\Campaign;

use Amasty\PushNotifications\Model\CampaignRepository;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Delete extends \Amasty\PushNotifications\Controller\Adminhtml\Campaign
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CampaignRepository
     */
    private $campaignRepository;

    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        CampaignRepository $campaignRepository
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->campaignRepository = $campaignRepository;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        if ($id) {
            try {
                $this->campaignRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the campaign.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete item right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
            }
        }

        $this->_redirect('*/*/');
    }
}
