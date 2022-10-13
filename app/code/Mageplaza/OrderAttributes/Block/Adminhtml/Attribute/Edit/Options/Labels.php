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

namespace Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit\Options;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\ResourceModel\Store\Collection;
use Mageplaza\OrderAttributes\Helper\Data;

/**
 * Class Labels
 * @package Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit\Options
 */
class Labels extends Template
{
    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var string
     */
    protected $_template = 'Mageplaza_OrderAttributes::attribute/labels.phtml';

    /**
     * Labels constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $helperData,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->helperData = $helperData;

        parent::__construct($context, $data);
    }

    /**
     * Retrieve stores collection with default store
     *
     * @return Collection
     */
    public function getStores()
    {
        if (!$this->hasStores()) {
            $this->setData('stores', $this->_storeManager->getStores());
        }

        return $this->_getData('stores');
    }

    /**
     * Retrieve frontend labels of attribute for each store
     *
     * @return array
     */
    public function getLabelValues()
    {
        $attrObj = $this->_registry->registry('entity_attribute');
        $values = [$attrObj->getFrontendLabel()];
        $storeLabels = $attrObj->getLabels() ? $this->helperData->jsonDecodeData($attrObj->getLabels()) : [];
        foreach ($this->getStores() as $store) {
            $storeId = (int)$store->getId();
            if ($storeId !== 0) {
                $values[$storeId] = isset($storeLabels[$storeId]) ? $storeLabels[$storeId] : '';
            }
        }

        return $values;
    }
}
