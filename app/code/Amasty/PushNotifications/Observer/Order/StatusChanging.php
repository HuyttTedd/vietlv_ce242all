<?php
declare(strict_types=1);

namespace Amasty\PushNotifications\Observer\Order;

use Amasty\PushNotifications\Exception\NotificationException;
use Amasty\PushNotifications\Model\OptionSource\Campaign\Events\SalesEvent;
use Amasty\PushNotifications\Model\Processor\CampaignProcessor;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;

class StatusChanging implements ObserverInterface
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
            $oldStatus = $order->getOrigData(OrderInterface::STATUS);
            $newStatus = $order->getStatus();
            if ($oldStatus !== $newStatus) {
                $this->campaignProcessor->processByEvent(SalesEvent::STATUS_CHANGING, [$order->getCustomerId()]);
            }
        } catch (NotificationException $exception) {
            $this->logger->critical($exception);
        }
    }
}
