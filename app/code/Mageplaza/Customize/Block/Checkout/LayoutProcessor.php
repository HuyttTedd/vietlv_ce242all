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
 * @package     Mageplaza_OrderAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Customize\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Swatches\Helper\Media;

/**
 * Class LayoutProcessor
 * @package Mageplaza\Customize\Block\Checkout
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var Media
     */
    protected $swatchHelper;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Media $swatchHelper
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Media $swatchHelper,
        Session $customerSession,
        StoreManagerInterface $storeManager
    ) {
        $this->swatchHelper = $swatchHelper;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     *
     * @return array
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function process($jsLayout)
    {
        $fieldset = &$jsLayout['components']['checkout']['children']['sidebar']['children']['summary']
        ['children']['itemsAfter']['children']['mpCustomizeAttributes']['children']['customize_note'];

        $fieldset = array (
            'component' => 'Magento_Ui/js/form/element/abstract',
            'fieldType' => 'text',
            'dataScope' => 'mpCustomizeAttributesSummary.customize_note',
            'label' => 'Customize Note',
            'options' =>
                array (
                ),
            'caption' => __('Please select an option'),
            'provider' => 'mpCustomizeCheckoutProvider',
            'visible' => true,
            'validation' =>
                array (
                ),
            'sortOrder' => '0',
            'default' => 'Your note here',
            'config' =>
                array (
                    'rows' => 5,
                    'additionalClasses' => '',
                    'customScope' => 'mpCustomizeAttributesSummary',
                    'elementTmpl' => 'ui/form/element/input',
                    'template' => 'ui/form/field',
                ),
        );

        return $jsLayout;
    }
}
