<?php
declare(strict_types=1);

namespace Amasty\PushNotifications\Observer\Customer;

use Amasty\PushNotifications\Exception\NotificationException;
use Amasty\PushNotifications\Model\OptionSource\Campaign\Events\CustomerEvent;
use Amasty\PushNotifications\Model\Processor\CampaignProcessor;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Psr\Log\LoggerInterface;

class GroupChanging implements ObserverInterface
{
    /**
     * @var CampaignProcessor
     */
    private $campaignProcessor;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        CampaignProcessor $campaignProcessor,
        CustomerRepositoryInterface $customerRepository,
        LoggerInterface $logger
    ) {
        $this->campaignProcessor = $campaignProcessor;
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        try {
            $customer = $observer->getCustomer();
            if (!$customer->getId()) {
                return;
            }
            $customerId = $customer->getId();
            $newGroupId = $customer->getGroupId();
            $oldGroupId = $this->customerRepository->getById($customerId)->getGroupId();
            if ($newGroupId !== $oldGroupId) {
                $this->campaignProcessor->processByEvent(CustomerEvent::GROUP_CHANGING, [$customerId]);
            }
        } catch (NotificationException $exception) {
            $this->logger->critical($exception);
        }
    }
}
