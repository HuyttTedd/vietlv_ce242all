<?php

namespace Amasty\PushNotifications\Controller\Firebase;

use Amasty\PushNotifications\Api\CampaignRepositoryInterface;
use Amasty\PushNotifications\Controller\RegistryConstants;
use Amasty\PushNotifications\Exception\NotificationException;
use Amasty\PushNotifications\Model\ConfigProvider;
use Amasty\PushNotifications\Model\Processor\SubscriberProcessor;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Webapi\Rest\Request;

class Counter extends Firebase
{
    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;

    public function __construct(
        Context $context,
        ConfigProvider $configProvider,
        Request $restRequest,
        SubscriberProcessor $subscriberProcessor,
        CampaignRepositoryInterface $campaignRepository
    ) {
        parent::__construct($context, $configProvider, $restRequest, $subscriberProcessor);
        $this->campaignRepository = $campaignRepository;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = [];

        try {
            $params = $this->getRequest()->getParams();

            if ($params && isset($params[RegistryConstants::CAMPAIGN_ID_PARAMS_KEY_NAME])) {
                $this->campaignRepository->increaseClickCounter(
                    $params[RegistryConstants::CAMPAIGN_ID_PARAMS_KEY_NAME]
                );
            }
        } catch (NotificationException $notificationException) {
            $result = [
                'status' => false,
                'message' => $notificationException->getMessage()
            ];
        }

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);

        return $resultJson;
    }
}
