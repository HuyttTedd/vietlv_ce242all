<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/


namespace Amasty\GiftCard\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @api
 */
interface GiftCardOptionInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constant defined for data array
     */
    public const GIFTCARD_AMOUNT = 'am_giftcard_amount';
    public const CUSTOM_GIFTCARD_AMOUNT = 'am_giftcard_amount_custom';
    public const GIFTCARD_TYPE = 'am_giftcard_type';
    public const SENDER_NAME = 'am_giftcard_sender_name';
    public const SENDER_EMAIL = 'am_giftcard_sender_email';
    public const RECIPIENT_NAME = 'am_giftcard_recipient_name';
    public const RECIPIENT_EMAIL = 'am_giftcard_recipient_email';
    public const RECIPIENT_PHONE = 'mobilenumber';
    public const IS_DATE_DELIVERY = 'is_date_delivery';
    public const DELIVERY_DATE = 'am_giftcard_date_delivery';
    public const DELIVERY_TIMEZONE = 'am_giftcard_date_delivery_timezone';
    public const MESSAGE = 'am_giftcard_message';
    public const IMAGE = 'am_giftcard_image';
    public const CUSTOM_IMAGE = 'am_giftcard_custom_image';
    public const USAGE = 'am_giftcard_usage';
    /**#@-*/

    /**
     * Created accounts code order item option key
     */
    public const GIFTCARD_CREATED_CODES = 'am_giftcard_created_codes';

    /**
     * @return float
     */
    public function getAmGiftcardAmount(): float;

    /**
     * @param float $cardAmount
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardOptionInterface
     */
    public function setAmGiftcardAmount($cardAmount): \Amasty\GiftCard\Api\Data\GiftCardOptionInterface;

    /**
     * @return float
     */
    public function getAmGiftcardAmountCustom(): float;

    /**
     * @param float|null $cardAmount
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardOptionInterface
     */
    public function setAmGiftcardAmountCustom($cardAmount): \Amasty\GiftCard\Api\Data\GiftCardOptionInterface;

    /**
     * @return int
     */
    public function getAmGiftcardType(): int;

    /**
     * @param int $type
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardOptionInterface
     */
    public function setAmGiftcardType($type): \Amasty\GiftCard\Api\Data\GiftCardOptionInterface;

    /**
     * @return string|null
     */
    public function getAmGiftcardSenderName();

    /**
     * @param string $senderName
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardOptionInterface
     */
    public function setAmGiftcardSenderName(string $senderName): \Amasty\GiftCard\Api\Data\GiftCardOptionInterface;

    /**
     * @return string|null
     */
    public function getAmGiftcardRecipientName();

    /**
     * @param string|null $recipientName
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardOptionInterface
     */
    public function setAmGiftcardRecipientName($recipientName): \Amasty\GiftCard\Api\Data\GiftCardOptionInterface;

    /**
     * @return string|null
     */
    public function getAmGiftcardRecipientEmail();

    /**
     * @param string|null $recipientEmail
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardOptionInterface
     */
    public function setAmGiftcardRecipientEmail($recipientEmail): \Amasty\GiftCard\Api\Data\GiftCardOptionInterface;

    /**
     * @return string|null
     */
    public function getMobilenumber();

    /**
     * @param string|null $recipientPhone
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardOptionInterface
     */
    public function setMobileNumber($recipientPhone): \Amasty\GiftCard\Api\Data\GiftCardOptionInterface;

    /**
     * @return string|null
     */
    public function getAmGiftcardDateDelivery();

    /**
     * @param string|null $deliveryDate
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardOptionInterface
     */
    public function setAmGiftcardDateDelivery($deliveryDate): \Amasty\GiftCard\Api\Data\GiftCardOptionInterface;

    /**
     * @return string|null
     */
    public function getAmGiftcardDateDeliveryTimezone();

    /**
     * @param string|null $deliveryTimezone
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardOptionInterface
     */
    public function setAmGiftcardDateDeliveryTimezone(
        $deliveryTimezone
    ): \Amasty\GiftCard\Api\Data\GiftCardOptionInterface;

    /**
     * @return string|null
     */
    public function getAmGiftcardMessage();

    /**
     * @param string|null $message
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardOptionInterface
     */
    public function setAmGiftcardMessage($message): \Amasty\GiftCard\Api\Data\GiftCardOptionInterface;

    /**
     * @return string|null
     */
    public function getAmGiftcardImage();

    /**
     * @param string|null $image
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardOptionInterface
     */
    public function setAmGiftcardImage($image): \Amasty\GiftCard\Api\Data\GiftCardOptionInterface;

    /**
     * @return string|null
     */
    public function getAmGiftcardCustomImage();

    /**
     * @param string|null $image
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardOptionInterface
     */
    public function setAmGiftcardCustomImage($image): GiftCardOptionInterface;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardOptionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Amasty\GiftCard\Api\Data\GiftCardOptionExtensionInterface $extensionAttributes
     *
     * @return \Amasty\GiftCard\Api\Data\GiftCardOptionInterface
     */
    public function setExtensionAttributes(
        \Amasty\GiftCard\Api\Data\GiftCardOptionExtensionInterface $extensionAttributes
    ): \Amasty\GiftCard\Api\Data\GiftCardOptionInterface;
}
