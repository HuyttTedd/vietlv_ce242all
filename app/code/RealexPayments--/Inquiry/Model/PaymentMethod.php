<?php

namespace RealexPayments\Inquiry\Model;

use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Payment\Transaction;
use RealexPayments\HPP\Api\RealexPaymentManagementInterface;
use RealexPayments\HPP\Api\RemoteXMLInterface;

class PaymentMethod
{
    const CLOSE_STATUS = 'closed';

    /**
     * @var RemoteXMLInterface
     */
    protected $remoteXml;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var RealexPaymentManagementInterface
     */
    protected $paymentManagement;

    /**
     * PaymentMethod constructor.
     * @param RemoteXMLInterface $remoteXml
     * @param OrderRepositoryInterface $orderRepository
     * @param RealexPaymentManagementInterface $paymentManagement
     */
    public function __construct(
        RemoteXMLInterface $remoteXml,
        OrderRepositoryInterface $orderRepository,
        RealexPaymentManagementInterface $paymentManagement
    ) {
        $this->remoteXml = $remoteXml;
        $this->orderRepository = $orderRepository;
        $this->paymentManagement = $paymentManagement;
    }

    /**
     * @param InfoInterface $payment
     * @param OrderInterface $order
     * @return self
     */
    public function reconcile(InfoInterface $payment, OrderInterface $order): self
    {
        $additionalInfo = $payment->getAdditionalInformation();
        if (!isset($additionalInfo['MERCHANT_ID']) || !isset($additionalInfo['ORDER_ID']) || !isset($additionalInfo['ACCOUNT'])) {
            $this->closeOrder($order);
            return $this;
        }
        $response = $this->remoteXml->query($payment);
        if (!$response) {
            $this->closeOrder($order);
            return $this;
        }

        $fields = $response->toArray();
        $fields['AMOUNT'] = $additionalInfo['AMOUNT'];
        if ($fields['RESULT'] != '00') {
            $this->closeOrder($order);
            return $this;
        }

        $payment->setTransactionId($fields['PASREF'])
            ->setParentTransactionId($payment->getAdditionalInformation('PASREF'))
            ->setTransactionAdditionalInfo(Transaction::RAW_DETAILS, $fields);

        $queryResponse   = $payment->getTransactionAdditionalInfo()[Transaction::RAW_DETAILS];
        $processResponse = $this->paymentManagement->processResponse($order, $queryResponse);
        if (!$processResponse) {
            $this->closeOrder($order);
        }
        return $this;
    }

    /**
     * @param OrderInterface $order
     * @return void
     */
    public function closeOrder(OrderInterface $order): void
    {
        $order->setState(self::CLOSE_STATUS)->setStatus(self::CLOSE_STATUS);
        $this->orderRepository->save($order);
    }
}
