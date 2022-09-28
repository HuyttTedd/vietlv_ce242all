<?php

namespace Amasty\PushNotifications\Plugin;

use Amasty\PushNotifications\Api\CampaignRepositoryInterface;
use Amasty\PushNotifications\Controller\RegistryConstants;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\UrlInterface;

class Action
{
    const URL_TRIM_CHARACTER = '/';

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Http
     */
    private $response;

    public function __construct(
        ResultFactory $resultFactory,
        EncryptorInterface $encryptor,
        CampaignRepositoryInterface $campaignRepository,
        UrlInterface $urlBuilder,
        Http $response
    ) {
        $this->resultFactory = $resultFactory;
        $this->encryptor = $encryptor;
        $this->campaignRepository = $campaignRepository;
        $this->urlBuilder = $urlBuilder;
        $this->response = $response;
    }

    /**
     * @param \Magento\Framework\App\FrontControllerInterface $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function aroundDispatch(
        \Magento\Framework\App\FrontControllerInterface $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        if ($campaignId = $this->checkIncreaseNotificationCounter($request)) {
            $campaign = $this->campaignRepository->getById($campaignId);
            $url = $campaign->getButtonNotificationUrl();
            if ($url) {
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $this->response->setNoCacheHeaders();
                $resultRedirect->setUrl($url);

                return $resultRedirect;
            }
        }

        return $proceed($request);
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return bool|int
     */
    private function checkIncreaseNotificationCounter(\Magento\Framework\App\RequestInterface $request)
    {
        $params = $request->getParams();

        if (isset($params[RegistryConstants::CLICK_COUNTER_FLAG_PARAM_NAME])
            && $params[RegistryConstants::CLICK_COUNTER_FLAG_PARAM_NAME]
            && isset($params[RegistryConstants::CAMPAIGN_ID_PARAMS_KEY_NAME])
        ) {
            $campaignId = (int)$this->encryptor->decrypt($params[RegistryConstants::CAMPAIGN_ID_PARAMS_KEY_NAME]);
            $this->campaignRepository->increaseClickCounter($campaignId);

            return $campaignId;
        }

        return false;
    }
}
