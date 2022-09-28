<?php
declare(strict_types=1);

namespace Amasty\PushNotifications\Observer\Customer;

use Amasty\PushNotifications\Exception\NotificationException;
use Amasty\PushNotifications\Model\Processor\CampaignProcessor;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class SendPush implements ObserverInterface
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
            $customer = $observer->getCustomer();
            if (!$customer->getId()) {
                return;
            }
            $this->campaignProcessor->processByEvent($observer->getEvent()->getName(), [$customer->getId()]);
        } catch (NotificationException $exception) {
            $this->logger->critical($exception);
        }
    }
}
