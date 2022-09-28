<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Block\Adminhtml\System\Config\InformationBlocks;

use \Amasty\Base\Block\Adminhtml\System\Config\InformationBlocks\UserGuide as BaseUserGuide;

/**
 * Temporary solution for user guide link in module config
 * must be removed when Amasty_GiftCard module appears in feed
 */
class UserGuide extends BaseUserGuide
{
    public const GUIDE_LINK = 'https://amasty.com/docs/doku.php?id=magento_2%3Agift_card&utm_source=extension&' .
    'utm_medium=backend&utm_campaign=gift-card_m2_guide';

    public function getUserGuideLink(): string
    {
        $link = parent::getUserGuideLink();
        if (!$link) {
            $link = self::GUIDE_LINK;
        }

        return $link;
    }
}
