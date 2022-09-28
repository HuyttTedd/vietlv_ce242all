<?php

namespace Amasty\PushNotifications\Model\Builder;

use Amasty\Geoip\Model\Geolocation;
use Amasty\PushNotifications\Api\Data\SubscriberInterface;
use Amasty\PushNotifications\Controller\RegistryConstants;
use Amasty\PushNotifications\Model\OptionSource\Campaign\Active;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Visitor;
use Magento\Framework\HTTP\Header;
use Amasty\Base\Model\GetCustomerIp;

class CustomerDataBuilder implements BuilderInterface
{
    /**
     * @var GetCustomerIp
     */
    private $customerIp;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var Header
     */
    private $httpHeader;

    /**
     * @var Geolocation
     */
    private $geolocation;

    /**
     * @var Visitor
     */
    private $visitor;

    public function __construct(
        Session $customerSession,
        Header $httpHeader,
        Geolocation $geolocation,
        Visitor $visitor,
        GetCustomerIp $customerIp
    ) {
        $this->customerSession = $customerSession;
        $this->httpHeader = $httpHeader;
        $this->geolocation = $geolocation;
        $this->visitor = $visitor;
        $this->customerIp = $customerIp;
    }

    /**
     * @inheritdoc
     */
    public function build(array $params)
    {
        $currentUserIp = $this->getCurrentIp();

        return [
            SubscriberInterface::SOURCE => $this->getBrowserFromUserAgent(),
            SubscriberInterface::LOCATION => $this->getLocation($currentUserIp),
            SubscriberInterface::VISITOR_ID => $this->visitor->getId() ? : null,
            SubscriberInterface::CUSTOMER_ID => $this->customerSession->getCustomerId() ? : null,
            SubscriberInterface::SUBSCRIBER_IP => $currentUserIp,
            SubscriberInterface::TOKEN => $params[RegistryConstants::USER_FIREBASE_TOKEN_PARAMS_KEY_NAME],
            SubscriberInterface::IS_ACTIVE => Active::STATUS_ACTIVE,
        ];
    }

    /**
     * @return string
     */
    private function getCurrentIp()
    {
        return $this->customerIp->getCurrentIp();
    }

    /**
     * @return string
     */
    private function getBrowserFromUserAgent()
    {
        $userAgent = $this->httpHeader->getHttpUserAgent();
        $browserDetector = new \Amasty\PushNotifications\Lib\Browser($userAgent);
        $currentBrowser = $browserDetector->getBrowser();

        return $currentBrowser ?: '';
    }

    /**
     * @param $ip
     * @return string
     */
    private function getLocation($ip)
    {
        $locationData = $this->geolocation->locate($ip)->getData();

        $locationPlaces = ['region', 'city', 'country'];
        $location = '';

        foreach ($locationPlaces as $place) {
            if (isset($locationData[$place]) && $locationData[$place]) {
                if ($location) {
                    $location .= ', ';
                }

                $location .= $locationData[$place];
            }
        }

        return $location;
    }
}
