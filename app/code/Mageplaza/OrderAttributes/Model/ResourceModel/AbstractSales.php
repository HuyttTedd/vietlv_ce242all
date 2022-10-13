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

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;

/**
 * Class AbstractSales
 * @package Mageplaza\OrderAttributes\Model\ResourceModel
 */
abstract class AbstractSales extends AbstractDb
{
    /**
     * @var Data
     */
    private $data;

    /**
     * AbstractSales constructor.
     *
     * @param Context $context
     * @param Data $data
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        Data $data,
        $connectionName = null
    ) {
        $this->data = $data;

        parent::__construct($context, $connectionName);
    }

    /**
     * Primary key auto increment flag
     *
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    /**
     * @param Attribute $attribute
     *
     * @return $this
     * @throws LocalizedException
     */
    public function createAttribute(Attribute $attribute)
    {
        switch ($attribute->getBackendType()) {
            case 'datetime':
                $definition = ['type' => Table::TYPE_DATE];
                break;
            case 'decimal':
                $definition = ['type' => Table::TYPE_DECIMAL, 'length' => '12,4'];
                break;
            case 'int':
                $definition = ['type' => Table::TYPE_INTEGER];
                break;
            case 'text':
                $definition = ['type' => Table::TYPE_TEXT];
                break;
            case 'varchar':
                $definition = ['type' => Table::TYPE_TEXT, 'length' => 255];
                break;
            default:
                return $this;
        }

        $definition['comment'] = ucwords(str_replace('_', ' ', $attribute->getAttributeCode()));

        $this->getConnection()->addColumn($this->getMainTable(), $attribute->getAttributeCode(), $definition);

        return $this;
    }

    /**
     * @param Attribute $attribute
     *
     * @return $this
     * @throws LocalizedException
     */
    public function deleteAttribute(Attribute $attribute)
    {
        $this->getConnection()->dropColumn($this->getMainTable(), $attribute->getAttributeCode());

        return $this;
    }

    /**
     * @param AbstractModel $object
     *
     * @return AbstractDb
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $this->data->applyFilter($object, 'input');

        return parent::_beforeSave($object);
    }

    /**
     * @param AbstractModel $object
     *
     * @return AbstractDb
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $this->data->applyFilter($object);

        return parent::_afterLoad($object);
    }
}
