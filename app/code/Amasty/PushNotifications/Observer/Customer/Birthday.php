<?php
declare(strict_types=1);

namespace Amasty\PushNotifications\Observer\Customer;

use Amasty\PushNotifications\Exception\NotificationException;
use Amasty\PushNotifications\Model\OptionSource\Campaign\Events\CustomerEvent;
use Amasty\PushNotifications\Model\Processor\CampaignProcessor;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Birthday implements ObserverInterface
{
    /**
     * @var CampaignProcessor
     */
    private $campaignProcessor;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        CampaignProcessor $campaignProcessor,
        TimezoneInterface $timezone,
        LoggerInterface $logger
    ) {
        $this->campaignProcessor = $campaignProcessor;
        $this->timezone = $timezone;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        try {
            $customer = $observer->getCustomer();
            if (!$customer->getId() || !$customer->getDob()) {
                return;
            }
            $dob = $this->timezone->date(new \DateTime($customer->getDob()))->format('m-d');
            if ($this->timezone->date()->format('m-d') === $dob) {
                $this->campaignProcessor->processByEvent(CustomerEvent::BIRTHDAY, [$customer->getId()]);
            }
        } catch (NotificationException $exception) {
            $this->logger->critical($exception);
        }
    }
}
