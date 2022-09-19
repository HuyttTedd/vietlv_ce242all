<?php

namespace Amasty\PushNotifications\Block\Adminhtml\Dashboard\Campaigns;

class Grid extends \Magento\Backend\Block\Dashboard\Grid
{
    /**
     * @var \Amasty\PushNotifications\Model\ResourceModel\Campaign\CollectionFactory
     */
    protected $collectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Amasty\PushNotifications\Model\ResourceModel\Campaign\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('lastCampaignsGrid');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->create()->dashboardGridFilter();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepares page sizes for dashboard grid with las 5 orders
     *
     * @return void
     */
    protected function _preparePage()
    {
        $this->getCollection()->setPageSize($this->getParam($this->getVarNameLimit(), $this->_defaultLimit));
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'sortable' => false,
                'index' => 'name',
            ]
        );

        $this->addColumn(
            'scheduled',
            [
                'header' => __('Scheduled At'),
                'type' => 'date',
                'sortable' => false,
                'index' => 'scheduled',
                'renderer' =>
                    \Amasty\PushNotifications\Block\Adminhtml\Dashboard\Campaigns\Grid\Renderer\ScheduledAt::class,
            ]
        );

        $this->addColumn(
            'clicked',
            [
                'header' => __('Clicks'),
                'sortable' => false,
                'type' => 'number',
                'index' => 'clicked',
                'renderer' => \Amasty\PushNotifications\Block\Adminhtml\Dashboard\Campaigns\Grid\Renderer\Clicks::class,
            ]
        );

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/campaign/edit', ['id' => $row->getId()]);
    }
}
