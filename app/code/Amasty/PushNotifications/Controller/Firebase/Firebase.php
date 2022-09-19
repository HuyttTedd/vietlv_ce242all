<?php

namespace Amasty\PushNotifications\Controller\Firebase;

use Amasty\PushNotifications\Model\ConfigProvider;
use Amasty\PushNotifications\Model\Processor\SubscriberProcessor;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\App\Action\Context;

abstract class Firebase extends \Magento\Framework\App\Action\Action
{
    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @var Request
     */
    protected $restRequest;

    /**
     * @var SubscriberProcessor
     */
    protected $subscriberProcessor;

    public function __construct(
        Context $context,
        ConfigProvider $configProvider,
        Request $restRequest,
        SubscriberProcessor $subscriberProcessor
    ) {
        parent::__construct($context);
        $this->configProvider = $configProvider;
        $this->restRequest = $restRequest;
        $this->subscriberProcessor = $subscriberProcessor;
    }

    /**
     * @return array
     */
    protected function ajaxParamsParse()
    {
        $params = [];
        parse_str((string)$this->restRequest->getContent(), $params);

        $this->getRequest()->setParams($params);

        return $params;
    }
}
