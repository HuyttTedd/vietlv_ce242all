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

namespace Mageplaza\OrderAttributes\Block\Adminhtml\Attribute;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Customer\Model\Attribute;
use Magento\Framework\Registry;

/**
 * Class Edit
 * @package Mageplaza\OrderAttributes\Block\Adminhtml\Attribute
 */
class Edit extends Container
{
    /**
     * @var string
     */
    protected $_blockGroup = 'Mageplaza_OrderAttributes';

    /**
     * @var string
     */
    protected $_controller = 'adminhtml_attribute';

    /**
     * Core registry
     *
     * @var Registry
     */
    public $_coreRegistry;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * @return Attribute
     */
    protected function getAttribute()
    {
        return $this->_coreRegistry->registry('entity_attribute');
    }

    /**
     * Construct
     */
    protected function _construct()
    {
        parent::_construct();

        $this->buttonList->add(
            'save_and_continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']]
                ]
            ]
        );
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        $attribute = $this->getAttribute();
        if ($attribute->getId()) {
            $label = $attribute->getFrontendLabel();

            return __('Edit Order Attribute "%1"', $label);
        }

        return __('New Order Attribute');
    }

    /**
     * Return validation url for edit form
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('mporderattributes/*/validate', ['_current' => true]);
    }

    /**
     * Return save url for edit form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('mporderattributes/*/save', ['_current' => true, 'back' => null]);
    }
}
