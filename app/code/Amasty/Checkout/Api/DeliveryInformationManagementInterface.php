<?php

namespace Amasty\Checkout\Api;

interface DeliveryInformationManagementInterface
{
    /**
     * @param int $cartId
     * @param string $date
     * @param int|null    $time
     * @param string|null $comment
     *
     * @return bool
     */
    public function update($cartId, $date, $time = -1, $comment = '');
}
