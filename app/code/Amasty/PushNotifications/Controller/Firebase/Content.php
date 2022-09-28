<?php

namespace Amasty\PushNotifications\Controller\Firebase;

use Amasty\PushNotifications\Model\ConfigProvider;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Element\Template;

class Content extends \Magento\Framework\App\Action\Action
{
    const JAVASCRIPT_CONTENT_TYPE_HEADER_VALUE = 'application/javascript';
    const FIREBASE_TEMPLATE_PATH = 'Amasty_PushNotifications::firebase_content.phtml';
    const DELETE_SCRIPT_TAGS_REGEXP = '#</?(?i:script|embed|object|frameset|frame|iframe|meta|link|style)(.|\n)*?>#';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Template
     */
    private $template;

    public function __construct(
        Context $context,
        ConfigProvider $configProvider,
        Template $template
    ) {
        parent::__construct($context);
        $this->configProvider = $configProvider;
        $this->template = $template;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $responseContent = $this->getResponseFirebaseContent();
        /** @var \Magento\Framework\Controller\Result\Raw $rawResponse */
        $rawResponse = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $rawResponse->setHttpResponseCode(200)
            ->setHeader('Content-Type', self::JAVASCRIPT_CONTENT_TYPE_HEADER_VALUE)
            ->setContents($responseContent);

        return $rawResponse;
    }

    /**
     * @return string
     */
    private function getResponseFirebaseContent()
    {
        /** @var Template $contentBlock */
        $contentBlock = $this->template->setTemplate(self::FIREBASE_TEMPLATE_PATH)
            ->setSenderId($this->configProvider->getSenderId());

        return preg_replace(self::DELETE_SCRIPT_TAGS_REGEXP, '', $contentBlock->toHtml());
    }
}
