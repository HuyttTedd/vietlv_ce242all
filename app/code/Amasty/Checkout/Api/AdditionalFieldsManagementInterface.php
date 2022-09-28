<?php

namespace Amasty\Checkout\Api;

interface AdditionalFieldsManagementInterface
{
    /**
     * @param int $cartId
     * @param \Amasty\Checkout\Api\Data\AdditionalFieldsInterface $fields
     *
     * @return bool
     */
    public function save($cartId, $fields);

    /**
     * @param int $quoteId
     *
     * @return \Amasty\Checkout\Api\Data\AdditionalFieldsInterface|\Amasty\Checkout\Model\AdditionalFields
     */
    public function getByQuoteId($quoteId);
}
