<?php

namespace Amasty\PushNotifications\Model\Api;

use Amasty\Base\Model\Serializer;
use Amasty\PushNotifications\Exception\NotificationException;
use Amasty\PushNotifications\Model\ConfigProvider;
use Amasty\PushNotifications\Model\Curl\Curl;

class Sender implements SenderInterface
{
    /**
     * @var string
     */
    private $contentType = 'application/json';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        ConfigProvider $configProvider,
        Curl $curl,
        Serializer $serializer
    ) {
        $this->configProvider = $configProvider;
        $this->curl = $curl;
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     */
    public function send(array $params, $storeId = null)
    {
        $response = [];

        try {
            $curlBody = $this->curlSend($params, $storeId);
            if ($curlBody) {
                $response = $this->serializer->unserialize($curlBody);
            }
        } catch (\Exception $exception) {
            throw new NotificationException(__($exception->getMessage()));
        }

        return $response;
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    private function getApiKey($storeId = null)
    {
        return $this->configProvider->getFirebaseApiKey($storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return array
     */
    private function getRequestHeaders($storeId = null)
    {
        return [
            'Content-Type' => $this->contentType,
            'Authorization' => 'key=' . $this->getApiKey($storeId),
        ];
    }

    /**
     * @param $data
     * @return string
     */
    private function prepareRequestBodyToSend($data)
    {
        return $this->serializer->serialize($data);
    }

    /**
     * @param array $params
     * @param int|null $storeId
     *
     * @return string
     */
    private function curlSend($params, $storeId = null)
    {
        $this->curl->setHeaders($this->getRequestHeaders($storeId));
        $this->curl->setOption(CURLOPT_FOLLOWLOCATION, 1);
        $this->curl->post($this->configProvider->getFirebaseApiRequestUrl(), $this->prepareRequestBodyToSend($params));

        return $this->curl->getBody();
    }
}
