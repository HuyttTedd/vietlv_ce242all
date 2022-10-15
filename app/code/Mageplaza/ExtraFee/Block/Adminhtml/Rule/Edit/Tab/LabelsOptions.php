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
 * @package     Mageplaza_ExtraFee
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ExtraFee\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Mageplaza\ExtraFee\Block\Adminhtml\Rule\Edit\Tab\Renderer\Labels;
use Mageplaza\ExtraFee\Block\Adminhtml\Rule\Edit\Tab\Renderer\Options;

/**
 * Class LabelsOptions
 * @package Mageplaza\ExtraFee\Block\Adminhtml\Rule\Edit\Tab
 */
class LabelsOptions extends Generic implements TabInterface
{
    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $this->addChild('labels', Labels::class);
        $this->addChild('options', Options::class);

        return parent::_prepareLayout();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Manage Label & Options');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getChildHtml(); // TODO: Change the autogenerated stub
    }
}
