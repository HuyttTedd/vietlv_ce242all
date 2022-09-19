<?php

namespace Amasty\Stripe\Gateway\Http\Client;

use Amasty\Stripe\Model\StripeAccountManagement;

/**
 * Class For Charge Transaction
 */
class Charge extends AbstractClient
{
    /**
     * @var StripeAccountManagement
     */
    private $stripeAccountManagement;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Payment\Model\Method\Logger $paymentLogger,
        \Amasty\Stripe\Model\Adapter\StripeAdapterProvider $adapterProvider,
        \Amasty\Stripe\Gateway\Config\Config $stripeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\State $state,
        StripeAccountManagement $stripeAccountManagement
    ) {
        parent::__construct(
            $logger,
            $paymentLogger,
            $adapterProvider,
            $stripeConfig,
            $checkoutSession,
            $request,
            $state
        );
        $this->stripeAccountManagement = $stripeAccountManagement;
    }

    /**
     * @param array $data
     *
     * @return \Stripe\ApiResource|\Stripe\Error\Base
     */
    protected function process(array $data)
    {
        if (!isset($data['source'])) {
            $data['source'] = $this->getSaveCardSource();
        }

        $storeId = null;
        if (!empty($data[AbstractClient::STORE_ID])) {
            $storeId = (int)$data[AbstractClient::STORE_ID];
            unset($data[AbstractClient::STORE_ID]);
        }

        if ((int)$data['save_card']
            && !empty($data['payment_method'])
            && $data['payment_method']
        ) {
            $paymentMethod = explode(":", $data['payment_method']);
            $this->stripeAccountManagement->processSaveCard($paymentMethod, $storeId);
        }

        $data['customer'] = $this->stripeAccountManagement->getCurrentStripeCustomerId($storeId);
        if ($this->isEmailReceiptsEnabled()) {
            $data['receipt_email'] = $this->getReceiptEmail();
        }
        $description = 'Order #' . $data['increment_id'] . ' by ' . $this->getReceiptEmail();
        if (!empty($data['description'])) {
            $description = $data['description']->getText();
        }

        $stripeAdapter = $this->adapterProvider->get($storeId);
        $stripeAdapter->intentPaymentUpdate($data['source'], ['description' => $description]);

        return $stripeAdapter->paymentIntentRetrieve($data['source']);
    }
}
