<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/


namespace Amasty\GiftCard\Model\GiftCard;

/**
 * Storage for keys of gift card attributes
 */
class Attributes
{
    public const GIFTCARD_PRICES = 'am_giftcard_prices';
    public const ALLOW_OPEN_AMOUNT = 'am_allow_open_amount';
    public const OPEN_AMOUNT_MIN = 'am_open_amount_min';
    public const OPEN_AMOUNT_MAX = 'am_open_amount_max';
    public const FEE_ENABLE = 'am_giftcard_fee_enable';
    public const FEE_TYPE = 'am_giftcard_fee_type';
    public const FEE_VALUE = 'am_giftcard_fee_value';
    public const GIFTCARD_TYPE = 'am_giftcard_type';
    public const GIFTCARD_LIFETIME = 'am_giftcard_lifetime';
    public const EMAIL_TEMPLATE = 'am_email_template';
    public const CODE_SET = 'am_giftcard_code_set';
    public const IMAGE = 'am_giftcard_code_image';
    public const USAGE = 'am_giftcard_usage';

    public const ATTRIBUTE_CONFIG_VALUE = '-1';
}
