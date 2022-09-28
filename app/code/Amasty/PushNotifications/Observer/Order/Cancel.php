<?php
declare(strict_types=1);

namespace Amasty\PushNotifications\Observer\Order;

use Amasty\PushNotifications\Exception\NotificationException;
use Amasty\PushNotifications\Model\Processor\CampaignProcessor;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class Cancel implements ObserverInterface
{
    /**
     * @var CampaignProcessor
     */
    private $campaignProcessor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        CampaignProcessor $campaignProcessor,
        LoggerInterface $logger
    ) {
        $this->campaignProcessor = $campaignProcessor;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        try {
            $order = $observer->getOrder();
            if (!$order->getCustomerId()) {
                return;
            }
            $this->campaignProcessor->processByEvent($observer->getEvent()->getName(), [$order->getCustomerId()]);
        } catch (NotificationException $exception) {
            $this->logger->critical($exception);
        }
    }
}
