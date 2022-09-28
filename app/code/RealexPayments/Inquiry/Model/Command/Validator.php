<?php

declare(strict_types=1);

namespace RealexPayments\Inquiry\Model\Command;

use Magento\Framework\Validation\ValidationException;
use RealexPayments\Inquiry\Model\Configuration;

/**
 * Class Validator.
 */
class Validator
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return void
     * @throws ValidationException
     */
    public function execute(): void
    {
        if (!$this->configuration->isRealexPaymentsInquiryEnabled()) {
            throw new ValidationException(__('RealexPayments Inquiry feature is disabled.'));
        }
    }
}
