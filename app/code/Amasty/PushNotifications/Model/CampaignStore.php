<?php

namespace Amasty\PushNotifications\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class CampaignStore extends AbstractExtensibleModel
{
    const ID = 'id';
    const STORE_ID = 'store_id';
    const CAMPAIGN_ID = 'campaign_id';

    /**
     * @var Campaign
     */
    private $campaign;

    /**
     * @var CampaignFactory
     */
    private $campaignFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        CampaignFactory $campaignFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
        $this->campaignFactory = $campaignFactory;
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\CampaignStore::class);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_getData(self::ID);
    }

    /**
     * @param int $id
     *
     * @return CampaignStore
     */
    public function setId($id)
    {
        $this->setData(self::ID, $id);

        return $this;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_getData(self::STORE_ID);
    }

    /**
     * @param int $storeId
     * @return CampaignStore
     */
    public function setStoreId($storeId)
    {
        $this->setData(self::STORE_ID, $storeId);

        return $this;
    }

    /**
     * @return int
     */
    public function getCampaignId()
    {
        return $this->_getData(self::CAMPAIGN_ID);
    }

    /**
     * @param int $campaignId
     * @return CampaignStore
     */
    public function setCampaignId($campaignId)
    {
        $this->setData(self::CAMPAIGN_ID, $campaignId);

        return $this;
    }

    /**
     * @param Campaign $campaign
     *
     * @return $this
     */
    public function setCampaign($campaign)
    {
        $this->campaign = $campaign;
        $this->setCampaignId($campaign->getId());
        return $this;
    }

    /**
     * @return Campaign
     */
    public function getCampaign()
    {
        if ($this->campaign === null && ($campaignId = $this->getCampaignId())) {
            $campaign = $this->campaignFactory->create();
            $campaign->load($campaignId);
            $this->setCampaign($campaign);
        }

        return $this->campaign;
    }
}
