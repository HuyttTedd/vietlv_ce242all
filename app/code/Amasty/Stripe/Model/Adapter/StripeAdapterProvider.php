<?php
declare(strict_types=1);

namespace Amasty\Stripe\Model\Adapter;

use Amasty\Stripe\Gateway\Config\Config as StripeConfig;

class StripeAdapterProvider
{
    /**
     * @var StripeAdapter
     */
    private $stripeAdapter;

    /**
     * @var StripeConfig
     */
    private $stripeConfig;

    /**
     * @var array
     */
    private $adapterByStore = [];

    public function __construct(StripeAdapter $stripeAdapter, StripeConfig $stripeConfig)
    {
        $this->stripeAdapter = $stripeAdapter;
        $this->stripeConfig = $stripeConfig;
    }

    /**
     * @param int|null $storeId
     * @return StripeAdapter
     */
    public function get(int $storeId = null): StripeAdapter
    {
        if (!empty($this->adapterByStore[$storeId])) {
            return $this->adapterByStore[$storeId];
        }

        $apiKey = $this->stripeConfig->getPrivateKey($storeId);
        $this->adapterByStore[$storeId] = $this->stripeAdapter->initCredentials($apiKey);

        return $this->adapterByStore[$storeId];
    }
}
