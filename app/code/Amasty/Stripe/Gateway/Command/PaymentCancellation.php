<?php

declare(strict_types=1);

namespace Amasty\Stripe\Gateway\Command;

use Amasty\Stripe\Model\Adapter\StripeAdapterProvider;
use Amasty\Stripe\Model\Ui\ConfigProvider;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\PaymentInterface;

class PaymentCancellation
{
    /**
     * @var StripeAdapterProvider
     */
    private $stripeAdapterProvider;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    public function __construct(StripeAdapterProvider $stripeAdapterProvider, CartRepositoryInterface $quoteRepository)
    {
        $this->stripeAdapterProvider = $stripeAdapterProvider;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param PaymentInterface $paymentMethod
     * @param int $cartId
     */
    public function execute(PaymentInterface $paymentMethod, $cartId): void
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $storeId = $quote->getStoreId();
        if ($paymentMethod->getMethod() === ConfigProvider::CODE
            && $paymentIntentId = $paymentMethod->getAdditionalData()['source']
        ) {
            $stripeAdapter = $this->stripeAdapterProvider->get($storeId);
            if ($intent = $stripeAdapter->paymentIntentRetrieve($paymentIntentId)) {
                if ($intent->capture_method === "manual") {
                    $stripeAdapter->paymentIntentCancel($intent);
                } else {
                    foreach ($intent->charges->data as $charge) {
                        $stripeAdapter->refundCreate(['charge' => $charge->id]);
                    }
                }
            }
        }
    }
}
