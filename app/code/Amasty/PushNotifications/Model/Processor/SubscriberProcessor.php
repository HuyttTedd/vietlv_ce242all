<?php

namespace Amasty\PushNotifications\Model\Processor;

use Amasty\PushNotifications\Api\Data\SubscriberInterface;
use Amasty\PushNotifications\Api\SubscriberRepositoryInterface;
use Amasty\PushNotifications\Controller\RegistryConstants;
use Amasty\PushNotifications\Model\Builder\CustomerDataBuilder;
use Amasty\PushNotifications\Model\SubscriberFactory;
use Magento\Store\Model\StoreManagerInterface;

class SubscriberProcessor
{
    /**
     * @var CustomerDataBuilder
     */
    private $customerDataBuilder;
    
    /**
     * @var SubscriberRepositoryInterface
     */
    private $subscriberRepository;

    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        CustomerDataBuilder $customerDataBuilder,
        SubscriberRepositoryInterface $subscriberRepository,
        SubscriberFactory $subscriberFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->customerDataBuilder = $customerDataBuilder;
        $this->subscriberRepository = $subscriberRepository;
        $this->subscriberFactory = $subscriberFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function process(array $params)
    {
        if (isset($params[RegistryConstants::USER_FIREBASE_TOKEN_PARAMS_KEY_NAME])
            && $params[RegistryConstants::USER_FIREBASE_TOKEN_PARAMS_KEY_NAME]
        ) {
            $token = $params[RegistryConstants::USER_FIREBASE_TOKEN_PARAMS_KEY_NAME];
            $customerData = $this->customerDataBuilder->build($params);

            if ($customerData[SubscriberInterface::CUSTOMER_ID] || $customerData[SubscriberInterface::VISITOR_ID]) {
                $subscriber = $this->subscriberRepository->getByCustomerVisitor(
                    $customerData[SubscriberInterface::CUSTOMER_ID],
                    $customerData[SubscriberInterface::VISITOR_ID]
                );
                if (!$subscriber) {
                    $subscriber = $this->subscriberFactory->create();
                }
                $subscriber->setStoreId($this->storeManager->getStore()->getId());

                if ($subscriber->getToken() != $token) {
                    $subscriber->addData($customerData);
                    $this->resetToken($subscriber, $token);
                } else {
                    $subscriber->addData($customerData);
                    $this->subscriberRepository->save($subscriber);
                }
            }
        }

        return $this;
    }

    /**
     * @param SubscriberInterface $subscriber
     * @param string $newToken
     *
     * @return $this
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function resetToken(SubscriberInterface $subscriber, $newToken)
    {
        $subscriber->setToken($newToken);
        $this->subscriberRepository->save($subscriber);

        return $this;
    }
}
