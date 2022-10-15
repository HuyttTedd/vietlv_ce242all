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

namespace Mageplaza\OrderAttributes\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Attribute
 * @package Mageplaza\OrderAttributes\Model\ResourceModel
 */
class Attribute extends AbstractDb
{
    /**
     * Date model
     *
     * @var DateTime
     */
    public $date;

    /**
     * Attribute constructor.
     *
     * @param Context $context
     * @param DateTime $date
     */
    public function __construct(
        Context $context,
        DateTime $date
    ) {
        $this->date = $date;

        parent::__construct($context);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_order_attribute', 'attribute_id');
    }

    /**
     * Before save callback
     *
     * @param AbstractModel $object
     *
     * @return $this|AbstractDb
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setUpdatedAt($this->date->date());

        return $this;
    }

    /**
     * Load attribute data by attribute code
     *
     * @param \Mageplaza\OrderAttributes\Model\Attribute $object
     * @param string $code
     *
     * @return bool
     */
    public function loadByCode(\Mageplaza\OrderAttributes\Model\Attribute $object, $code)
    {
        $select = $this->_getLoadSelect('attribute_code', $code, $object);
        $data = $this->getConnection()->fetchRow($select);

        if ($data) {
            $object->setData($data);

            return true;
        }

        return false;
    }

    /**
     * @param string $col
     *
     * @return bool
     */
    public function checkSalesOrderColumn($col)
    {
        $table = $this->getTable('sales_order');

        return $this->getConnection()->tableColumnExists($table, $col);
    }
}
