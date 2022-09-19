<?php
declare(strict_types=1);

namespace Amasty\Stripe\Gateway\Response;

use Amasty\Stripe\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Handle For Cancel And Void order action
 */
class VoidCancelHandler extends RefundHandler
{

    /**
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);

        /** @var Payment $orderPayment */
        $orderPayment = $paymentDO->getPayment();

        if ($orderPayment instanceof Payment) {

            $this->setRefundId(
                $orderPayment,
                $response['object']->charges->data[0]->refunds->data[0]
            );

            $orderPayment->setIsTransactionClosed($this->shouldCloseTransaction());
            $orderPayment->setShouldCloseParentTransaction(
                $this->shouldCloseParentTransaction($orderPayment)
            );
        }
    }
}
