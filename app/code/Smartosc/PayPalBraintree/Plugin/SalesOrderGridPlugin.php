<?php

declare(strict_types=1);

namespace Smartosc\PayPalBraintree\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection;
use Smartosc\PayPalBraintree\Model\Configuration;

class SalesOrderGridPlugin
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
     * @param Collection $subject
     * @return null
     * @throws LocalizedException
     */
    public function beforeLoad(Collection $subject)
    {
        if (!$subject->isLoaded() && !$this->configuration->isPaypalBraintreeEnabled()) {
            $primaryKey = $subject->getResource()->getIdFieldName();
            $tableName = $subject->getResource()->getTable('braintree_transaction_details');

            $subject->getSelect()->joinLeft(
                $tableName,
                $tableName . '.order_id = main_table.' . $primaryKey,
                $tableName . '.transaction_source'
            );
        }

        return null;
    }
}
