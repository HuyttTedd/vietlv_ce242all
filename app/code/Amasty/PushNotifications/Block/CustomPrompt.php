<?php

namespace Amasty\PushNotifications\Block;

use Amasty\PushNotifications\Controller\RegistryConstants;
use Amasty\PushNotifications\Model\ConfigProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Template;
use Amasty\PushNotifications\Model\DeviceDetect;

class CustomPrompt extends Template
{
    const URL_PART_TRIM_CHARACTER = '/';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DeviceDetect
     */
    private $deviceDetector;

    public function __construct(
        Template\Context $context,
        ConfigProvider $configProvider,
        DeviceDetect $deviceDetector,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
        $this->deviceDetector = $deviceDetector;
        $this->request = $context->getRequest();
    }

    private function isShowPromptForCurrentDevice(): bool
    {
        return in_array($this->deviceDetector->detectDevice(), $this->configProvider->getDevicesAvailableForPrompt());
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getCustomPromptContent()
    {
        return __($this->configProvider->getCustomPromptText());
    }

    public function getCustomPromptCssClass(): string
    {
        return $this->configProvider->getCustomPromptPosition();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getErrorMessage()
    {
        return __('Oops! Notifications are disabled.');
    }

    public function getSenderId(): string
    {
        return (string)$this->configProvider->getSenderId();
    }

    public function getSubscribeActionUrl(): string
    {
        return $this->getUrl(RegistryConstants::FIREBASE_SUBSCRIBE_URL_PATH);
    }

    public function getPromptFrequency(): int
    {
        return (int)$this->configProvider->getCustomPromptFrequency();
    }

    public function getPromptDelay(): int
    {
        return $this->configProvider->getCustomPromptDelay();
    }

    public function getMaxNotificationsPerDay(): int
    {
        return $this->configProvider->getMaxNotificationsPerCustomerDaily();
    }

    public function isEnableModule(): bool
    {
        return $this->configProvider->isModuleEnable();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->isCustomPromptAvailable()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * @return bool
     */
    private function isCustomPromptAvailable()
    {
        $availablePages = $this->configProvider->getCustomPromptAvailablePages();

        return $this->configProvider->isCustomPromptEnable()
            && $this->isShowPromptForCurrentDevice()
            && ($this->checkAvailablePage($availablePages) || $this->configProvider->getPromptAvailableOnAllPages());
    }

    /**
     * @param $availablePages
     *
     * @return bool
     */
    private function checkAvailablePage($availablePages)
    {
        if ($availablePages) {
            $currentUrlPath = trim($this->request->getOriginalPathInfo(), self::URL_PART_TRIM_CHARACTER);
            $uri = trim($this->request->getRequestUri(), self::URL_PART_TRIM_CHARACTER);

            foreach ($availablePages as $pattern) {
                switch ($pattern == self::URL_PART_TRIM_CHARACTER) {
                    case 1:
                        if ($currentUrlPath == '') {
                            return true;
                        }

                        break;
                    case 0:
                        $pattern = trim($pattern, self::URL_PART_TRIM_CHARACTER);

                        if (preg_match("|$pattern|", $currentUrlPath) || preg_match("|$pattern|", $uri)) {
                            return true;
                        }

                        break;
                }
            }
        }

        return false;
    }
}
