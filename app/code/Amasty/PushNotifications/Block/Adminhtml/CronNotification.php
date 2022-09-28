<?php

namespace Amasty\PushNotifications\Block\Adminhtml;

class CronNotification extends \Magento\Backend\Block\Widget\Container
{
    /**
     * Url
     */
    const URL_CRON = 'https://amasty.com/blog/configure-magento-cron-job';

    /**
     * @var string
     */
    protected $_template = 'cron_notification.phtml';

    /**
     * @var \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory
     */
    private $cronCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory $cronCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->cronCollectionFactory = $cronCollectionFactory;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $crontabCollection = $this->cronCollectionFactory->create();

        if ($crontabCollection->getSize() !== 0) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getNotificationMessage()
    {
        return __('Magento cron doesn\'t seem to be running. Please check <a target="_blank" href="%1">this article</a>
                   to learn why Magento cron is important and how to configure it.', $this->getSectionLink());
    }

    /**
     * @return string
     */
    private function getSectionLink()
    {
        return self::URL_CRON;
    }
}
