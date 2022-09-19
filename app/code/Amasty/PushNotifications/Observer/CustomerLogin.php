<?php

namespace Amasty\PushNotifications\Observer;

use Amasty\PushNotifications\Model\SubscriberRepository;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Session\SessionManagerInterface;

class CustomerLogin implements ObserverInterface
{
    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @var SubscriberRepository
     */
    private $subscriberRepository;

    public function __construct(
        SessionManagerInterface $session,
        SubscriberRepository $subscriberRepository
    ) {
        $this->session = $session;
        $this->subscriberRepository = $subscriberRepository;
    }

    public function execute(Observer $observer)
    {
        if (!($visitorId = $this->session->getVisitorData()['visitor_id'] ?? null)) {
            return;
        }
        $customerId = $observer->getCustomer()->getId();

        try {
            $subscriber = $this->subscriberRepository->getByCustomerVisitor(null, $visitorId);

            if ($subscriber) {
                $subscriber->setCustomerId($customerId);
                $this->subscriberRepository->save($subscriber);
            }
        } catch (LocalizedException $e) {
            return;
        }
    }
}
