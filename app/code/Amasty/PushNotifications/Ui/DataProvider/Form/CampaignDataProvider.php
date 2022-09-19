<?php

namespace Amasty\PushNotifications\Ui\DataProvider\Form;

use Amasty\PushNotifications\Api\CampaignRepositoryInterface;
use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Amasty\PushNotifications\Model\FileUploader\FileInfoCollector;
use Amasty\PushNotifications\Model\ResourceModel\Campaign\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class CampaignDataProvider extends AbstractDataProvider
{
    /**
     * @var CampaignRepositoryInterface
     */
    private $repository;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var FileInfoCollector
     */
    private $fileInfoCollector;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        CampaignRepositoryInterface $repository,
        DataPersistorInterface $dataPersistor,
        FileInfoCollector $fileInfoCollector,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->repository = $repository;
        $this->dataPersistor = $dataPersistor;
        $this->fileInfoCollector = $fileInfoCollector;
    }

    /**
     * Get data
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData()
    {
        $data = parent::getData();

        /**
         * It is need for support of several fieldsets.
         * For details @see \Magento\Ui\Component\Form::getDataSourceData
         */
        if ($data['totalRecords'] > 0) {
            $campaignId = (int)$data['items'][0][CampaignInterface::CAMPAIGN_ID];
            /** @var \Amasty\PushNotifications\Model\Campaign $campaignModel */
            $campaignModel = $this->repository->getById($campaignId);
            $campaignData = $campaignModel->getData();
            $data[$campaignId] = $campaignData;
            $data[$campaignId][CampaignInterface::LOGO_PATH] = $campaignModel->getIsDefaultLogo()
                ? []
                : $this->getLogoData($campaignModel->getLogoPath());
            $data[$campaignId][CampaignInterface::STORES] = $campaignModel->getStores();
            $data[$campaignId][CampaignInterface::CUSTOMER_GROUPS] = $campaignModel->getCustomerGroups();
            $data[$campaignId][CampaignInterface::EVENTS] = $campaignModel->getEvents();
            $data[$campaignId][CampaignInterface::CUSTOMER_SEGMENTS] = $campaignModel->getSegments();
        }

        if ($savedData = $this->dataPersistor->get('campaignData')) {
            $savedCampaignId = isset($savedData['campaign_id']) ? $savedData['campaign_id'] : null;
            if (isset($data[$savedCampaignId])) {
                $data[$savedCampaignId] = array_merge($data[$savedCampaignId], $savedData);
            } else {
                $data[$savedCampaignId] = $savedData;
            }

            $this->dataPersistor->clear('campaignData');
        }

        return $data;
    }

    /**
     * @param $filePath
     * @return array|null
     */
    private function getLogoData($filePath)
    {
        return $this->fileInfoCollector->getInfoByFilePath($filePath);
    }
}
