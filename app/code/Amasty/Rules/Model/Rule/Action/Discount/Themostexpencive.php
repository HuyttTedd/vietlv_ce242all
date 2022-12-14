<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Rules
*/


namespace Amasty\Rules\Model\Rule\Action\Discount;

/**
 * Amasty Rule calculation by action.
 * @see \Amasty\Rules\Helper\Data::TYPE_EXPENCIVE
 */
class Themostexpencive extends Thecheapest
{
    public const RULE_VERSION = '1.0.0';
    public const DEFAULT_SORT_ORDER = 'desc';
}
