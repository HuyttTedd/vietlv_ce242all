<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterTierPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BetterTierPrice\Block\Adminhtml\Product\Edit\Action\Attribute\Tab;

use Magento\Backend\Block\Widget;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Phrase;

/**
 * Class TierPrice
 * @package Mageplaza\BetterTierPrice\Block\Adminhtml\Product\Edit\Action\Attribute\Tab
 */
class TierPrice extends Widget implements TabInterface
{
    /**
     * Tab settings
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('Tier Price');
    }

    /**
     * @return Phrase
     */
    public function getTabTitle()
    {
        return __('Tier Price');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        return parent::toHtml() . $this->getChildHtml();
    }
}
