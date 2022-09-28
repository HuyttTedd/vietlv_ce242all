<?php

namespace Amasty\PushNotifications\Block\Adminhtml\Campaign\Edit;

use Amasty\PushNotifications\Controller\RegistryConstants;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Amasty\PushNotifications\Model\ConfigProvider;
use Magento\Framework\App\RequestInterface;

class SaveAndSendButton implements ButtonProviderInterface
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var FormKey
     */
    private $formKey;

    public function __construct(
        Registry $coreRegistry,
        UrlInterface $urlBuilder,
        ConfigProvider $configProvider,
        RequestInterface $request,
        FormKey $formKey
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->urlBuilder = $urlBuilder;
        $this->configProvider = $configProvider;
        $this->request = $request;
        $this->formKey = $formKey;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        if (!$this->configProvider->isModuleEnable() ||  !$this->request->getParam('id')) {
            return [];
        }

        return [
            'label' => __('Send Test Notification'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'Amasty_PushNotifications/js/push_test' => [
                        'urlAction' => $this->getSendTestActionUrl(),
                        'senderId' => $this->configProvider->getSenderId(),
                        'campaignId' => (int)$this->request->getParam('id'),
                        'formKey' => $this->formKey->getFormKey(),
                    ]
                ],
            ],
            'on_click' => '',
            'sort_order' => 60
        ];
    }

    /**
     * @return string
     */
    private function getSendTestActionUrl()
    {
        return $this->urlBuilder->getUrl('*/*/sendTest');
    }
}
