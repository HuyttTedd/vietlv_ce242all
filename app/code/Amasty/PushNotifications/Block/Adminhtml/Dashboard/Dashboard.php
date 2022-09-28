<?php

namespace Amasty\PushNotifications\Block\Adminhtml\Dashboard;

use Amasty\PushNotifications\Model\OptionSource\Campaign\Status;
use Magento\Backend\Block\Template\Context;
use Amasty\PushNotifications\Model\ResourceModel\Campaign\CollectionFactory as CampaignCollectionFactory;
use Amasty\PushNotifications\Model\ResourceModel\Subscriber\CollectionFactory as SubscriberCollectionFactory;

class Dashboard extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'dashboard/index.phtml';

    /**
     * @var CampaignCollectionFactory
     */
    private $campaignCollectionFactory;

    /**
     * @var SubscriberCollectionFactory
     */
    private $subscriberCollectionFactory;

    public function __construct(
        Context $context,
        CampaignCollectionFactory $campaignCollectionFactory,
        SubscriberCollectionFactory $subscriberCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->campaignCollectionFactory = $campaignCollectionFactory;
        $this->subscriberCollectionFactory = $subscriberCollectionFactory;
    }

    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->addChild('lastCampaigns', \Amasty\PushNotifications\Block\Adminhtml\Dashboard\Campaigns\Grid::class);
        $this->addChild('grids', \Magento\Backend\Block\Dashboard\Grids::class);

        parent::_prepareLayout();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getCampaignGridTitle()
    {
        return __('Latest campaigns');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getAddCampaignButtonTitle()
    {
        return __('+ Start a new Campaign');
    }

    /**
     * @return string
     */
    public function getAddCampaignButtonLink()
    {
        return $this->getUrl('*/campaign/edit');
    }

    /**
     * @return int
     */
    public function getFinishedCampaigns()
    {
        return $this->campaignCollectionFactory->create()->addFilterByStatus(Status::STATUS_PASSED)->getSize();
    }

    /**
     * @return int
     */
    public function getSubscribersCount()
    {
        return $this->subscriberCollectionFactory->create()->getSize();
    }

    /**
     * @return int
     */
    public function getLatestCampaignRate()
    {
        /** @var \Amasty\PushNotifications\Api\Data\CampaignInterface $campaign */
        $campaign = $this->campaignCollectionFactory->create()->addCommonFiltersForDashboard()->getFirstItem();

        return $campaign ? $this->getCampaignClickRateInPercent($campaign) : 0;
    }

    /**
     * @param \Amasty\PushNotifications\Api\Data\CampaignInterface $campaign
     * @return float|int
     */
    private function getCampaignClickRateInPercent(\Amasty\PushNotifications\Api\Data\CampaignInterface $campaign)
    {
        return $campaign->getCampaignId() && $campaign->getShownCounter() !== 0
            ? number_format((($campaign->getClickedCounter() / $campaign->getShownCounter()) * 100), 2)
            : 0;
    }

    /**
     * @return int
     */
    public function getClicksTotal()
    {
        return $this->campaignCollectionFactory->create()->getClicksTotal();
    }

    /**
     * @return bool
     */
    public function isCountIncreased()
    {
        /** @var \Amasty\PushNotifications\Api\Data\CampaignInterface $campaign */
        $campaign = $this->campaignCollectionFactory->create()->addCommonFiltersForDashboard()->getSecondItem();
        $campaignClickRate = $campaign ? $this->getCampaignClickRateInPercent($campaign) : 0;

        return $this->getLatestCampaignRate() > $campaignClickRate;
    }
}
