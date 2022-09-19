<?php

namespace Amasty\Stripe\Model;

use Amasty\Stripe\Gateway\Helper\AmountHelper;
use Amasty\Stripe\Gateway\Config\Config as StripeConfig;
use Amasty\Stripe\Model\Adapter\StripeAdapterProvider;

/**
 * Adapter for Payment Intent Stripe
 */
class PaymentIntentRegistry
{
    /**
     * @var AmountHelper
     */
    private $amountHelper;

    /**
     * @var StripeAdapterProvider
     */
    private $stripeAdapterProvider;

    /**
     * @var StripeConfig
     */
    private $stripeConfig;

    /**
     * @var StripeAccountManagement
     */
    private $accountManager;

    public function __construct(
        AmountHelper $amountHelper,
        StripeAdapterProvider $stripeAdapterProvider,
        StripeConfig $stripeConfig,
        StripeAccountManagement $accountManager
    ) {
        $this->amountHelper = $amountHelper;
        $this->stripeAdapterProvider = $stripeAdapterProvider;
        $this->stripeConfig = $stripeConfig;
        $this->accountManager = $accountManager;
    }

    /**
     * @param float $grandTotal
     * @param string $currency
     * @param int|null $storeId
     * @return array
     */
    public function getPaymentIntentsDataSecret($grandTotal, $currency, int $storeId = null)
    {
        $grandTotal = $this->amountHelper->getAmountForStripe($grandTotal, $currency);
        $stripeAdapter = $this->stripeAdapterProvider->get($storeId);
        $authorizeMethod = $this->stripeConfig->getAuthorizeMethod($storeId);
        $customer = $this->accountManager->getCurrentStripeCustomerId($storeId);

        $params = [
            'amount' => $grandTotal,
            'currency' => $currency,
            'payment_method_types' => ["card"],
            'capture_method' => $authorizeMethod == 'authorize' ? 'manual' : 'automatic',
            'customer' => $customer
        ];
        $intent = $stripeAdapter->paymentIntentCreate($params);

        return ['pi' => $intent->id, 'secret' => $intent->client_secret];
    }
}
