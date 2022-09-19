<?php

namespace Amasty\PushNotifications\Ui\Component\Listing\Column;

use Amasty\PushNotifications\Controller\RegistryConstants;
use Amasty\PushNotifications\Model\ConfigProvider;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class TestNotification extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        ConfigProvider $configProvider,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->configProvider = $configProvider;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');

            foreach ($dataSource['data']['items'] as &$item) {
                if (!$this->configProvider->isModuleEnable()) {
                    $item[$fieldName . '_html'] = '-';
                } else {
                    $item[$fieldName . '_html'] = "<button class='button'><span>"
                        . $this->getButtonLabel() . "</span></button>";
                    $item[$fieldName . '_urlAction'] = $this->urlBuilder
                        ->getUrl('amasty_notifications/campaign/sendTest');
                    $item[$fieldName . '_senderId'] = $this->configProvider->getSenderId();
                }
            }
        }

        return $dataSource;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    private function getButtonLabel()
    {
        return __('Send Test Notification');
    }
}
