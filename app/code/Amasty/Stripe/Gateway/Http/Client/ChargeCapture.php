<?php

namespace Amasty\Stripe\Gateway\Http\Client;

use Amasty\Stripe\Gateway\Request\ChargeCaptureDataBuilder;

/**
 * Charge Invoice in Stripe
 */
class ChargeCapture extends AbstractClient
{
    /**
     * @param array $data
     *
     * @return \Stripe\ApiResource|\Stripe\Error\Base
     */
    protected function process(array $data)
    {
        $chargeId = $data[ChargeCaptureDataBuilder::CHARGE_ID];
        unset($data[ChargeCaptureDataBuilder::CHARGE_ID]);

        $storeId = null;
        if (!empty($data[AbstractClient::STORE_ID])) {
            $storeId = (int)$data[AbstractClient::STORE_ID];
            unset($data[AbstractClient::STORE_ID]);
        }

        $stripeAdapter = $this->adapterProvider->get($storeId);

        return $stripeAdapter->chargeCapture($chargeId, $data);
    }
}
