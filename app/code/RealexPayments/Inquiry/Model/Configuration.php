<?php

declare(strict_types=1);

namespace RealexPayments\Inquiry\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Configuration.
 */
class Configuration
{
    const XML_PATH_REALEX_PAYMENTS_INQUIRY_ENABLED = 'realexpayments_inquiry/general/enabled';

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
    public function isRealexPaymentsInquiryEnabled(): bool
    {
        if (!isset($this->caches[self::XML_PATH_REALEX_PAYMENTS_INQUIRY_ENABLED])) {
            $this->caches[self::XML_PATH_REALEX_PAYMENTS_INQUIRY_ENABLED] = $this->scopeConfig->isSetFlag(
                self::XML_PATH_REALEX_PAYMENTS_INQUIRY_ENABLED
            );
        }

        return $this->caches[self::XML_PATH_REALEX_PAYMENTS_INQUIRY_ENABLED];
    }
}
