<?php

namespace Amasty\Stripe\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Data Builder for Capture
 */
class CaptureDataBuilder implements BuilderInterface
{
    const CAPTURE = 'capture';

    /**
     * @param array $buildSubject
     *
     * @return array
     */
    public function build(array $buildSubject)
    {
        return [
            self::CAPTURE => true,
        ];
    }
}
