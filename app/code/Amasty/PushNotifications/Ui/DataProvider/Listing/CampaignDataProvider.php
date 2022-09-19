<?php

namespace Amasty\PushNotifications\Ui\DataProvider\Listing;

use Amasty\PushNotifications\Api\CampaignRepositoryInterface;
use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Amasty\PushNotifications\Model\ResourceModel\Campaign\CollectionFactory;

class CampaignDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var CampaignRepositoryInterface
     */
    private $repository;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        CampaignRepositoryInterface $repository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->collection->addExpressionFieldToSelect(
            'click_rate',
            'IF({{shown}} = 0, 0, ROUND({{clicked}} / {{shown}} * 100, 2))',
            [
                'clicked' => CampaignInterface::CLICKED_COUNTER,
                'shown' => CampaignInterface::SHOWN_COUNTER
            ]
        );
        $this->repository = $repository;
    }

    /**
     * Get data
     *
     * @return array
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData()
    {
        $data = parent::getData();
        foreach ($data['items'] as $key => $campaign) {
            $campaign = $this->repository->getById($campaign['campaign_id']);
            $campaignData = $campaign->getData();
            $campaignData['store_id'] = $campaign->getStores();
            $campaignData['click_rate'] = $data['items'][$key]['click_rate'];
            $data['items'][$key] = $campaignData;
        }

        return $data;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getField() == 'click_rate') {
            $filter->setField(new \Zend_Db_Expr('IF(shown = 0, 0, ROUND(clicked / shown * 100, 2))'));
        }

        parent::addFilter($filter);
    }
}
