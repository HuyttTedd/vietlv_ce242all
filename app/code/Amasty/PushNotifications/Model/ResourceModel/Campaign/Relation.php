<?php

namespace Amasty\PushNotifications\Model\ResourceModel\Campaign;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Exception\CouldNotSaveException;
use Amasty\PushNotifications\Model\CampaignStoreRepository;

class Relation implements RelationInterface
{
    /**
     * @var CampaignStoreRepository
     */
    private $campaignStoreRepository;

    /**
     * Relation constructor.
     *
     * @param CampaignStoreRepository $campaignStoreRepository
     */
    public function __construct(
        CampaignStoreRepository $campaignStoreRepository
    ) {
        $this->campaignStoreRepository = $campaignStoreRepository;
    }

    /**
     * @param AbstractModel $object
     *
     * @throws CouldNotSaveException
     */
    public function processRelation(AbstractModel $object)
    {
        if (null !== $object->getStores()) {
            foreach ($object->getStores() as $store) {
                if (!$store->getCampaignId()) {
                    $store->setCampaign($object);
                }
                $this->campaignStoreRepository->save($store);
            }
        }
    }
}
