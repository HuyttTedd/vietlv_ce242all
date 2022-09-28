<?php

declare(strict_types=1);

namespace RealexPayments\Inquiry\Model\Inquiry;

use RealexPayments\Inquiry\Model\ResourceModel\Order as OrderResourceInquiry;

/**
 * Class Handler.
 */
class Handler
{
    /**
     * @var OrderResourceInquiry
     */
    protected $orderResource;

    /**
     * @var Processor
     */
    protected $processor;

    /**
     * Handler constructor.
     * @param OrderResourceInquiry $orderResource
     * @param Processor $processor
     */
    public function __construct(
        OrderResourceInquiry $orderResource,
        Processor $processor
    ) {
        $this->orderResource = $orderResource;
        $this->processor = $processor;
    }

    /**
     * @return void
     */
    public function handleInquiry(): void
    {
        $orderIds = $this->orderResource->getOrderIdsForInquiry();
        if ($orderIds) {
            $this->processor->reconcileOrders($orderIds);
        }
    }
}
