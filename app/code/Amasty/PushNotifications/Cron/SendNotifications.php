<?php

namespace Amasty\PushNotifications\Cron;

use Amasty\PushNotifications\Exception\NotificationException;
use Amasty\PushNotifications\Model\ConfigProvider;
use Amasty\PushNotifications\Model\Processor\CampaignProcessor;
use Psr\Log\LoggerInterface;

class SendNotifications
{
    /**
     * @var CampaignProcessor
     */
    private $campaignProcessor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        CampaignProcessor $campaignProcessor,
        LoggerInterface $logger,
        ConfigProvider $configProvider
    ) {
        $this->campaignProcessor = $campaignProcessor;
        $this->logger = $logger;
        $this->configProvider = $configProvider;
    }

    /**
     * @return $this
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        try {
            if (!$this->configProvider->isModuleEnable()) {
                throw new NotificationException(__('Module is disabled'));
            }

            $this->campaignProcessor->processBySchedule();
        } catch (NotificationException $exception) {
            $this->logger->critical($exception);
        }

        return $this;
    }
}
