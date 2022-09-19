<?php

namespace Amasty\PushNotifications\Controller\Adminhtml\Campaign;

use Amasty\PushNotifications\Api\CampaignRepositoryInterface;
use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Amasty\PushNotifications\Model\CampaignFactory;
use Amasty\PushNotifications\Model\FileUploader\FileProcessor;
use Amasty\PushNotifications\Model\OptionSource\Campaign\NotificationType;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Amasty\PushNotifications\Controller\Adminhtml\Campaign
{
    /**
     * @var CampaignRepositoryInterface
     */
    private $repository;

    /**
     * @var CampaignFactory
     */
    private $campaignFactory;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    public function __construct(
        Context $context,
        CampaignRepositoryInterface $repository,
        CampaignFactory $campaignFactory,
        DataPersistorInterface $dataPersistor,
        FileProcessor $fileProcessor
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->campaignFactory = $campaignFactory;
        $this->dataPersistor = $dataPersistor;
        $this->fileProcessor = $fileProcessor;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     *
     * @throws \Exception
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            $data = [];
            try {
                /** @var \Amasty\PushNotifications\Model\Campaign $model */
                $model = $this->campaignFactory->create();
                $data = $this->prepareData($this->getRequest()->getPostValue());
                $logoData = [];

                if (isset($data[CampaignInterface::LOGO_PATH])) {
                    $logoData = $data[CampaignInterface::LOGO_PATH];
                    unset($data[CampaignInterface::LOGO_PATH]);
                }

                $model->addData($data);
                $this->repository->save($model);
                $this->saveLogoImage($model, $logoData);

                if ($this->getRequest()->getParam('save_and_send')) {
                    if (!empty($model->getEmail())) {
                        $this->getRequest()->setParams(['id' => $model->getId()]);
                        $this->_forward('send');

                        return;
                    }
                    $this->messageManager->addWarningMessage(__('Email can not be sent. Email field is empty.'));
                }

                $this->messageManager->addSuccessMessage(__('You saved the item.'));

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId()]);

                    return;
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->dataPersistor->set('campaignData', $data);

                if ($model->getId()) {
                    $this->_redirect(
                        '*/*/edit',
                        $model->getId() ? ['id' => $model->getId()] : []
                    );
                }

                return;
            }
        }

        $this->_redirect('*/*/');
    }

    /**
     * @param $campaignModel
     * @param $logoData
     *
     * @return $this
     *
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Exception
     */
    private function saveLogoImage($campaignModel, $logoData)
    {
        if ($logoData) {
            $logoData = array_shift($logoData);
            $campaignId = $campaignModel->getCampaignId();
            $logoPath = $this->fileProcessor->saveTmp($logoData, $campaignId);

            if ($logoPath) {
                $campaignModel->setLogoPath($logoPath);
                $this->repository->save($campaignModel);
            }
        }

        return $this;
    }

    /**
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    private function prepareData($data)
    {
        if (isset($data[CampaignInterface::SCHEDULED], $data[CampaignInterface::NOTIFICATION_TYPE])
            && $data[CampaignInterface::NOTIFICATION_TYPE] === NotificationType::CRON_TYPE
        ) {
            $data[CampaignInterface::SCHEDULED] = new \DateTime($data[CampaignInterface::SCHEDULED]);
        } else {
            $data[CampaignInterface::SCHEDULED] = null;
        }

        $data[CampaignInterface::IS_DEFAULT_LOGO] = (int)!isset($data[CampaignInterface::LOGO_PATH]);

        return $data;
    }
}
