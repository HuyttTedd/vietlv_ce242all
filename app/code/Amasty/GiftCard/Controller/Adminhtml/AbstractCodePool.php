<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Controller\Adminhtml;

abstract class AbstractCodePool extends \Magento\Backend\App\Action
{
    public const ADMIN_RESOURCE = 'Amasty_GiftCard::giftcard_code';
}
