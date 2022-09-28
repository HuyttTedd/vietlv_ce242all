<?php

namespace Amasty\PushNotifications\Controller\Adminhtml\Campaign;

use Amasty\PushNotifications\Controller\RegistryConstants;
use Amasty\PushNotifications\Exception\NotificationException;
use Amasty\PushNotifications\Model\Processor\NotificationProcessor;
use Amasty\PushNotifications\Model\Processor\SubscriberProcessor;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\Webapi\Rest\Request;

class SendTest extends \Amasty\PushNotifications\Controller\Adminhtml\Campaign
{
    /**
     * @var Request
     */
    private $restRequest;

    /**
     * @var NotificationProcessor
     */
    private $notificationProcessor;

    /**
     * @var SubscriberProcessor
     */
    private $subscriberProcessor;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Action\Context $context,
        Request $restRequest,
        NotificationProcessor $notificationProcessor,
        SubscriberProcessor $subscriberProcessor,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->restRequest = $restRequest;
        $this->notificationProcessor = $notificationProcessor;
        $this->subscriberProcessor = $subscriberProcessor;
        $this->registry = $registry;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     *
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $result = [];
        $this->registry->register(RegistryConstants::REGISTRY_TEST_NOTIFICATION_NAME, 1);

        try {
            if (isset($params['campaignId'])
                && isset($params[RegistryConstants::USER_FIREBASE_TOKEN_PARAMS_KEY_NAME])
            ) {
                $result = $this->notificationProcessor->processByToken(
                    (int)$params['campaignId'],
                    $params['userToken'],
                    true
                );

                $this->subscriberProcessor->process($params);
            }
        } catch (NotificationException $exception) {
            $result = [
                'status' => false,
                'message' => $exception->getMessage()
            ];
        }

        $this->registry->unregister(RegistryConstants::REGISTRY_TEST_NOTIFICATION_NAME);
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);

        return $resultJson;
    }

    /**
     * Check url keys. If non valid - redirect
     *
     * @return bool
     */
    public function _processUrlKeys()
    {
        $this->getRequest()->setParams($this->getParamsFromRequestContent());

        return parent::_processUrlKeys();
    }

    /**
     * @return array
     */
    private function getParamsFromRequestContent()
    {
        $params = [];
        parse_str((string)$this->restRequest->getContent(), $params);

        return $params;
    }
}
