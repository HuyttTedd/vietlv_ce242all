<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/


namespace Amasty\GiftCard\Api\Data;

interface GiftCardEmailInterface
{
    public const GIFT_CODE = 'gift_code';
    public const RECIPIENT_NAME = 'recipient_name';
    public const RECIPIENT_EMAIL = 'recipient_email';
    public const SENDER_NAME = 'sender_name';
    public const SENDER_EMAIL = 'sender_email';
    public const SENDER_MESSAGE = 'sender_message';
    public const BALANCE = 'balance';
    public const EXPIRED_DATE = 'expired_date';
    public const IMAGE = 'image';
    public const EXPIRY_DAYS = 'expiry_days';
    public const IS_ALLOW_ASSIGN_TO_CUSTOMER = 'is_allow_assign_to_customer';

    /**
     * @return string
     */
    public function getGiftCode(): string;

    /**
     * @param string $code
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardEmailInterface
     */
    public function setGiftCode(string $code): \Amasty\GiftCard\Api\Data\GiftCardEmailInterface;

    /**
     * @return string|null
     */
    public function getRecipientName();

    /**
     * @param string $name
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardEmailInterface
     */
    public function setRecipientName(string $name): \Amasty\GiftCard\Api\Data\GiftCardEmailInterface;

    /**
     * @return string
     */
    public function getRecipientEmail(): string;

    /**
     * @param string $email
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardEmailInterface
     */
    public function setRecipientEmail(string $email): \Amasty\GiftCard\Api\Data\GiftCardEmailInterface;

    /**
     * @return string|null
     */
    public function getSenderName();

    /**
     * @param string $name
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardEmailInterface
     */
    public function setSenderName(string $name): \Amasty\GiftCard\Api\Data\GiftCardEmailInterface;

    /**
     * @return string|null
     */
    public function getSenderEmail();

    /**
     * @param string $email
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardEmailInterface
     */
    public function setSenderEmail(string $email): \Amasty\GiftCard\Api\Data\GiftCardEmailInterface;

    /**
     * @return string|null
     */
    public function getSenderMessage();

    /**
     * @param string $message
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardEmailInterface
     */
    public function setSenderMessage(string $message): \Amasty\GiftCard\Api\Data\GiftCardEmailInterface;

    /**
     * @return string
     */
    public function getBalance(): string;

    /**
     * @param string $balance
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardEmailInterface
     */
    public function setBalance(string $balance): \Amasty\GiftCard\Api\Data\GiftCardEmailInterface;

    /**
     * @return string|null
     */
    public function getExpiredDate();

    /**
     * @param string|null $date
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardEmailInterface
     */
    public function setExpiredDate($date): \Amasty\GiftCard\Api\Data\GiftCardEmailInterface;

    /**
     * @return string|null
     */
    public function getImage();

    /**
     * @param string $image
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardEmailInterface
     */
    public function setImage(string $image): \Amasty\GiftCard\Api\Data\GiftCardEmailInterface;

    /**
     * @return int
     */
    public function getExpiryDays(): int;

    /**
     * @param int $days
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardEmailInterface
     */
    public function setExpiryDays(int $days): \Amasty\GiftCard\Api\Data\GiftCardEmailInterface;

    /**
     * @return bool
     */
    public function isAllowAssignToCustomer(): bool;

    /**
     * @param bool $allowAssign
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardEmailInterface
     */
    public function setIsAllowAssignToCustomer(bool $isAllowAssign): \Amasty\GiftCard\Api\Data\GiftCardEmailInterface;
}
