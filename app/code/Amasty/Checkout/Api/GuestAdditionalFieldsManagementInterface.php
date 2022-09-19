<?php

namespace Amasty\Checkout\Api;

interface GuestAdditionalFieldsManagementInterface
{
    /**
     * @param string $cartId
     * @param \Amasty\Checkout\Api\Data\AdditionalFieldsInterface $fields
     *
     * @return bool
     */
    public function save($cartId, $fields);
}
