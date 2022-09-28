<?php

namespace Amasty\PushNotifications\Controller\Firebase;

use Amasty\PushNotifications\Controller\RegistryConstants;
use Amasty\PushNotifications\Exception\NotificationException;
use Magento\Framework\Controller\ResultFactory;

class Subscribe extends Firebase
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        try {
            $params = $this->ajaxParamsParse();

            if (isset($params[RegistryConstants::USER_FIREBASE_TOKEN_PARAMS_KEY_NAME])) {
                $this->subscriberProcessor->process($params);
            }

            $result = [
                'status' => true,
                'message' => __('User is successfully subscribed'),
            ];
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
