<?php

namespace Amasty\GiftCardAccount\Api\Data;

interface GiftCardAccountInterface
{
    public const ACCOUNT_ID = 'account_id';
    public const CODE_ID = 'code_id';
    public const IMAGE_ID = 'image_id';
    public const WEBSITE_ID = 'website_id';
    public const ORDER_ITEM_ID = 'order_item_id';
    public const STATUS = 'status';
    public const INITIAL_VALUE = 'initial_value';
    public const CURRENT_VALUE = 'current_value';
    public const EXPIRED_DATE = 'expired_date';
    public const COMMENT = 'comment';
    public const DATE_DELIVERY = 'date_delivery';
    public const IS_SENT = 'is_sent';
    public const CUSTOMER_CREATED_ID = 'customer_created_id';
    public const CODE_MODEL = 'code_model';
    public const CODE_POOL = 'code_pool';
    public const RECIPIENT_EMAIL = 'recipient_email';
    public const RECIPIENT_PHONE = 'mobilenumber';
    public const IS_REDEEMABLE = 'is_redeemable';
    public const USAGE = 'usage';

    /**
     * @return int
     */
    public function getAccountId(): int;

    /**
     * @param int $id
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setAccountId(int $id): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

    /**
     * @return int
     */
    public function getCodeId(): int;

    /**
     * @param int $id
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setCodeId(int $id): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

    /**
     * @return int
     */
    public function getImageId(): int;

    /**
     * @param int $id
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setImageId(int $id): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

    /**
     * @return int
     */
    public function getOrderItemId(): int;

    /**
     * @param int|null $id
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setOrderItemId($id): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

    /**
     * @return int
     */
    public function getWebsiteId(): int;

    /**
     * @param int $id
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setWebsiteId(int $id): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @param int $status
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setStatus(int $status): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

    /**
     * @return float
     */
    public function getInitialValue(): float;

    /**
     * @param float $value
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setInitialValue(float $value): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

    /**
     * @return float
     */
    public function getCurrentValue(): float;

    /**
     * @param float $value
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setCurrentValue(float $value): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

    /**
     * @return string|null
     */
    public function getExpiredDate();

    /**
     * @param string|null $date
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setExpiredDate($date): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

    /**
     * @return string|null
     */
    public function getComment();

    /**
     * @param string $comment
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setComment(string $comment): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

    /**
     * @param string $date
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setDeliveryDate(string $date): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

    /**
     * @return string|null
     */
    public function getDeliveryDate();

    /**
     * @param bool $isSent
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setIsSent(bool $isSent): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

    /**
     * @return bool
     */
    public function isSent(): bool;

    /**
     * @param int|null $id
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setCustomerCreatedId($id): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

    /**
     * @return int|null
     */
    public function getCustomerCreatedId();

    /**
     * @param \Amasty\GiftCard\Api\Data\CodeInterface $code
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setCodeModel(
        \Amasty\GiftCard\Api\Data\CodeInterface $code
    ): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

    /**
     * @return \Amasty\GiftCard\Api\Data\CodeInterface|null
     */
    public function getCodeModel();

    /**
     * @return int|null
     */
    public function getCodePool();

    /**
     * @param int $codePoolId
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setCodePool(int $codePoolId): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

    /**
     * @return string
     */
    public function getRecipientEmail(): string;

    /**
     * @param string $email
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setRecipientEmail(string $email): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

    /**
     * @return string|null
     */
    public function getRecipientPhone(): ?string;

    /**
     * @param string|null $phone
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setRecipientPhone(?string $phone): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

    /**
     * @param bool|null $isRedeemable
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setIsRedeemable(?bool $isRedeemable): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

    /**
     * @return bool|null
     */
    public function isRedeemable(): ?bool;

    /**
     * @return string
     */
    public function getUsage(): string;

    /**
     * @param string $usage
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function setUsage(string $usage): \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
}
