<?php
declare(strict_types=1);

namespace Amasty\PushNotifications\Observer\Customer;

use Amasty\PushNotifications\Exception\NotificationException;
use Amasty\PushNotifications\Model\OptionSource\Campaign\Events\NewsletterEvent;
use Amasty\PushNotifications\Model\Processor\CampaignProcessor;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Newsletter\Model\Subscriber;
use Psr\Log\LoggerInterface;

class Newsletter implements ObserverInterface
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
            $subscriber = $observer->getSubscriber();
            if (!$subscriber->getCustomerId()) {
                return;
            }
            switch ($subscriber->getStatus()) {
                case Subscriber::STATUS_SUBSCRIBED:
                    $this->campaignProcessor->processByEvent(
                        NewsletterEvent::SUBSCRIPTION,
                        [$subscriber->getCustomerId()]
                    );
                    break;
                case Subscriber::STATUS_UNSUBSCRIBED:
                    $this->campaignProcessor->processByEvent(
                        NewsletterEvent::SUBSCRIPTION_CANCELLATION,
                        [$subscriber->getCustomerId()]
                    );
                    break;
            }
        } catch (NotificationException $exception) {
            $this->logger->critical($exception);
        }
    }
}
