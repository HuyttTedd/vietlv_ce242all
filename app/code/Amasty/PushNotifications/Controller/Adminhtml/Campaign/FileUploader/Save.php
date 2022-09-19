<?php

namespace Amasty\PushNotifications\Controller\Adminhtml\Campaign\FileUploader;

use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Amasty\PushNotifications\Model\FileUploader\FileProcessor;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Amasty\PushNotifications\Controller\Adminhtml\Campaign
{
    /**
     * @var FileProcessor
     */
    protected $fileProcessor;

    /**
     * @param Context $context
     * @param FileProcessor $fileProcessor
     */
    public function __construct(
        Context $context,
        FileProcessor $fileProcessor
    ) {
        parent::__construct($context);
        $this->fileProcessor = $fileProcessor;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $result = $this->fileProcessor->saveToTmp(CampaignInterface::LOGO_PATH);
        } catch (LocalizedException $exception) {
            $result = ['error' => $exception->getMessage()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
