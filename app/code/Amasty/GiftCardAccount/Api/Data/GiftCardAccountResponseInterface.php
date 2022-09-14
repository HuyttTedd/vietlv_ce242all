<?php

namespace Amasty\GiftCardAccount\Api\Data;

/**
 * Gift Card account response interface.
 * Used for messages about transactions with the account.
 * @api
 */
interface GiftCardAccountResponseInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    public const ACCOUNT = 'account';
    public const MESSAGES = 'messages';
    /**#@-*/

    /**
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function getAccount(): GiftCardAccountInterface;

    /**
     * @param \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface $account
     * @return $this
     */
    public function setAccount(GiftCardAccountInterface $account): GiftCardAccountResponseInterface;

    /**
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardMessageInterface[]|null
     */
    public function getMessages(): ?array;

    /**
     * @param \Amasty\GiftCardAccount\Api\Data\GiftCardMessageInterface[] $messages
     * @return $this
     */
    public function setMessages(array $messages): GiftCardAccountResponseInterface;

    /**
     * @param \Amasty\GiftCardAccount\Api\Data\GiftCardMessageInterface $message
     * @return void
     */
    public function addMessage(GiftCardMessageInterface $message): void;
}
