<?php

namespace Amasty\Stripe\Gateway\Request;

use Amasty\Stripe\Gateway\Http\Client\AbstractClient;
use Amasty\Stripe\Gateway\Helper\AmountHelper;
use Amasty\Stripe\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Building Data For Refund
 */
class RefundDataBuilder implements BuilderInterface
{
    /**
     * Key for get charge
     */
    const CHARGE = 'charge';

    /**
     * Key for get amount
     */
    const AMOUNT = 'amount';

    /**
     * @var AmountHelper
     */
    protected $amountHelper;

    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * @param AmountHelper $amountHelper
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        AmountHelper $amountHelper,
        SubjectReader $subjectReader
    ) {
        $this->subjectReader = $subjectReader;
        $this->amountHelper = $amountHelper;
    }

    /**
     * @param array $buildSubject
     *
     * @return array
     */
    public function build(array $buildSubject)
    {
        $chargeId = $amount = null;
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $order = $paymentDO->getOrder();

        try {
            $chargeId = $this->subjectReader->readPayment($buildSubject)->getPayment()->getLastTransId();
            $amount = $this->amountHelper->getAmountForStripe(
                $this->subjectReader->readAmount($buildSubject),
                $order->getCurrencyCode()
            );
            $storeId = (int)$order->getStoreId();

        } catch (\InvalidArgumentException $e) {
            return [];
        }

        return [
            self::CHARGE => $chargeId,
            self::AMOUNT => $amount,
            AbstractClient::STORE_ID => $storeId
        ];
    }
}
