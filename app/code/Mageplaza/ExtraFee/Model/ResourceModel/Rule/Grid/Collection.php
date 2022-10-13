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

namespace Mageplaza\ExtraFee\Model\ResourceModel\Rule\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\Store\Model\Store;
use Mageplaza\ExtraFee\Model\Config\Source\DisplayArea;
use Mageplaza\ExtraFee\Model\ResourceModel\Rule;
use Psr\Log\LoggerInterface as Logger;
use Zend_Db_Expr;

/**
 * Class Collection
 * @package Mageplaza\ExtraFee\Model\ResourceModel\Rule\Grid
 */
class Collection extends SearchResult
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'rule_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_extrafee_rule_grid_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'rule_grid_collection';

    // phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod

    /**
     * Collection constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     *
     * @throws LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'mageplaza_extrafee_rule',
        $resourceModel = Rule::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @param array|string $field
     * @param null $condition
     *
     * @return SearchResult
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'customer_groups_ids') {
            $field     = 'customer_groups';
            $condition = ['finset' => $condition['eq']];
        }
        if ($field === 'stores') {
            $field     = 'store_ids';
            $condition = [
                ['finset' => $condition['eq']],
                ['finset' => Store::DEFAULT_STORE_ID]
            ];
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @param string $field
     * @param string $direction
     *
     * @return $this|Collection
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if ($field === 'area') {
            $values = [DisplayArea::CART_SUMMARY, DisplayArea::PAYMENT_METHOD, DisplayArea::SHIPPING_METHOD];
            $this->getSelect()->order(
                new Zend_Db_Expr('FIELD(main_table.area, ' . implode(',', $values) . ') ' . $direction)
            );
        } else {
            parent::setOrder($field, $direction); // TODO: Change the autogenerated stub
        }

        return $this;
    }
}