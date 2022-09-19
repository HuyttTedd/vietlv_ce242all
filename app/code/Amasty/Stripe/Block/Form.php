<?php

namespace Amasty\Stripe\Block;

use Magento\Backend\Model\Session\Quote;
use Magento\Payment\Block\Form\Cc;

class Form extends Cc
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Stripe::order/stripe.phtml';

    /**
     * @var \Amasty\Stripe\Gateway\Config\Config
     */
    private $config;

    /**
     * @var \Amasty\Stripe\Model\StripeAccountManagement
     */
    private $stripeAccountManagement;

    /**
     * @var Quote
     */
    private $backendQuoteSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        \Amasty\Stripe\Gateway\Config\Config $config,
        \Amasty\Stripe\Model\StripeAccountManagement $stripeAccountManagement,
        Quote $backendQuoteSession,
        array $data = []
    ) {
        parent::__construct($context, $paymentConfig, $data);
        $this->config = $config;
        $this->stripeAccountManagement = $stripeAccountManagement;
        $this->backendQuoteSession = $backendQuoteSession;
    }

    /**
     * @inheritDoc
     */
    public function getJsLayout()
    {
        $storeId = (int)$this->backendQuoteSession->getQuote()->getStoreId();
        $this->jsLayout['components'] = null;
        $cardsData = $this->stripeAccountManagement->getAllCards($storeId);

        $result = [
            'component' => 'Amasty_Stripe/js/view/customer/order/stripe-form',
            'sdkUrl' => $this->config->getSdkUrl($storeId),
            'publicKey' => $this->config->getPublicKey($storeId),
            'cardsData' => $cardsData,
            'threedSecureAlways' => $this->config->getThreedSecureAlways($storeId),
            'currency' => $this->getCurrentCurrency(),
            'secretUrl' => $this->config->getSecretUrl()
        ];

        $this->jsLayout['components']['amasty-stripe-saved-cards'] = $result;

        return \Zend_Json::encode($this->jsLayout);
    }

    /**
     * @return string
     */
    private function getCurrentCurrency()
    {
        return $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
    }
}
