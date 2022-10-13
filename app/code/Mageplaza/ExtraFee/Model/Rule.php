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

namespace Mageplaza\ExtraFee\Model;

use Magento\SalesRule\Model\Rule as AbstractModel;
use Mageplaza\ExtraFee\Model\ResourceModel\Rule as RuleResource;

/**
 * Class Rule
 * @package Mageplaza\ExtraFee\Model
 * @method getOptions()
 * @method getFeeTax()
 * @method getRefundable()
 * @method getArea()
 * @method getApplyType()
 * @method getStopFurtherProcessing()
 * @method getStatus()
 * @method getStoreIds()
 * @method getCustomerGroups()
 * @method setType($feeType)
 * @method getFeeType()
 * @method getLabels()
 * @method setOptions($jsonEncode)
 */
class Rule extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageplaza_extrafee_rule';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mageplaza_extrafee_rule';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_extrafee_rule';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(RuleResource::class);
    }
}
