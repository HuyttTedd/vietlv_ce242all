<?php

declare(strict_types=1);

namespace RealexPayments\Inquiry\Model\Inquiry;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use RealexPayments\Inquiry\Model\PaymentMethod;
use Psr\Log\LoggerInterface;

/**
 * Class Processor.
 */
class Processor
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var PaymentMethod
     */
    protected $paymentInquiry;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Processor constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param PaymentMethod $paymentInquiry
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        PaymentMethod $paymentInquiry,
        LoggerInterface $logger
    ) {
        $this->orderRepository  = $orderRepository;
        $this->paymentInquiry   = $paymentInquiry;
        $this->logger           = $logger;
    }

    /**
     * @param array $orderIds
     * @return $this
     */
    public function reconcileOrders(array $orderIds): self
    {
        foreach ($orderIds as $id) {
            try {
                $order = $this->orderRepository->get($id);
                if ($order) {
                    $payment = $order->getPayment();
                    /** @var InfoInterface $payment */
                    $this->paymentInquiry->reconcile($payment, $order);
                }
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }

        return $this;
    }
}
