<?php

declare(strict_types=1);

namespace Smartosc\PayPalBraintree\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Configuration.
 */
class Configuration
{
    const XML_PATH_PAYPAL_BRAINTREE_ENABLED = 'smartosc_paypal_braintree/general/enabled';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var array
     */
    protected $caches = [];

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isPaypalBraintreeEnabled(): bool
    {
        if (!isset($this->caches[self::XML_PATH_PAYPAL_BRAINTREE_ENABLED])) {
            $this->caches[self::XML_PATH_PAYPAL_BRAINTREE_ENABLED] = $this->scopeConfig->isSetFlag(
                self::XML_PATH_PAYPAL_BRAINTREE_ENABLED
            );
        }

        return $this->caches[self::XML_PATH_PAYPAL_BRAINTREE_ENABLED];
    }
}
