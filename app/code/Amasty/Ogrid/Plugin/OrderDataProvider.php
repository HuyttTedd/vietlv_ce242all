<?php

namespace Amasty\Ogrid\Plugin;

use Amasty\Ogrid\Model\Attribute;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\FilterGroupFactory;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;

class OrderDataProvider
{
    const SALES_ORDER_GRID_DATA_SOURCE = 'sales_order_grid_data_source';
    const GREATER_THAN_CONDITION = 'gteq';
    const LESS_THAN_CONDITION = 'lteq';

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Ui\Api\BookmarkManagementInterface
     */
    protected $bookmarkManagement;

    /**
     * @var bool|null
     */
    protected $export;

    /**
     * @var array
     */
    protected $visibleColumns = [
        'amasty_ogrid_base_subtotal',
        'amasty_ogrid_subtotal',
        'amasty_ogrid_total_due',
        'amasty_ogrid_total_paid',
        'amasty_ogrid_tax_amount'
    ];

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $orderItemCollectionFactory;

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var \Amasty\Ogrid\Helper\Data
     */
    protected $helper;

    /**
     * @var array
     */
    protected $orderConfig = [];

    /**
     * @var array
     */
    protected $filterData = [];

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory
     */
    protected $shipmentTrackCollectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var FilterGroupFactory
     */
    protected $filterGroupFactory;

    /**
     * @var array
     */
    protected $nullCompareFilters = [];

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Ui\Api\BookmarkManagementInterface $bookmarkManagement,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $shipmentTrackCollectionFactory,
        \Amasty\Ogrid\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request,
        FilterGroupFactory $filterGroupFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->bookmarkManagement = $bookmarkManagement;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->shipmentTrackCollectionFactory = $shipmentTrackCollectionFactory;
        $this->helper = $helper;
        $this->filterData = $context->getFiltersParams();
        $this->request = $request;
        $this->filterGroupFactory = $filterGroupFactory;
        $this->registry = $registry;
        $request = $this->request->getParams();
        $this->appendVisibleColumns();

        if (isset($request['data'])) {
            $data = json_decode($request['data'], true);
            if (isset($data['column'])) {
                $this->visibleColumns[] = $data['column'];
            }
        }
    }

    protected function isOrderGrid(\Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider $dataProvider)
    {
        return $dataProvider->getName() == self::SALES_ORDER_GRID_DATA_SOURCE;
    }

    protected function appendVisibleColumns()
    {
        $bookmarks = $this->bookmarkManagement->loadByNamespace('sales_order_grid');

        /** @var \Magento\Ui\Api\Data\BookmarkInterface $bookmark */
        foreach ($bookmarks->getItems() as $bookmark) {
            if (isset($bookmark->getConfig()['current']['columns'])) {
                $columns = $bookmark->getConfig()['current']['columns'];
                $this->prepareColumns($columns);
            } elseif (isset($bookmark->getConfig()['views'][$bookmark->getIdentifier()]['data']['columns'])) {
                $columns = $bookmark->getConfig()['views'][$bookmark->getIdentifier()]['data']['columns'];
                $this->prepareColumns($columns);
            }
        }
    }

    private function prepareColumns($columns)
    {
        foreach ($columns as $key => $column) {
            if (isset($column['visible']) && $column['visible']) {
                $this->visibleColumns[] = $key;
            }
        }
    }

    private function isColumnVisible($column)
    {
        return in_array($column, $this->visibleColumns);
    }

    public function beforeAddFilter(
        \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider $dataProvider,
        \Magento\Framework\Api\Filter $filter
    ) {
        if ($this->isOrderGrid($dataProvider)) {
            if (array_key_exists($filter->getField(), $this->helper->getOrderFields())
                && $this->isColumnVisible($filter->getField())
            ) {
                $this->getColumn($filter->getField())->changeFilter($filter);
            } else {
                if ($filter->getField() != 'is_preorder') {
                    if (strpos($filter->getField(), '.') !== false) {
                        $filter->setField($filter->getField());
                    }
                }
            }
        }
    }

    protected function isExport()
    {
        if ($this->export === null) {
            $this->export = $this->request->getControllerName() === 'export';
        }

        return $this->export;
    }

    public function afterGetSearchCriteria(DataProvider $subject, SearchCriteria $searchCriteria): SearchCriteria
    {
        if ($this->isOrderGrid($subject)) {
            $updatedFilterGroups = $this->extractFiltersForNullCompare($searchCriteria);

            if (count($this->nullCompareFilters)) {
                $searchCriteria->setFilterGroups($updatedFilterGroups);
            }
        }

        return $searchCriteria;
    }

    public function afterGetSearchResult(
        \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider $dataProvider,
        $collection
    ) {
        if ($this->isOrderGrid($dataProvider)) {
            if (count($this->nullCompareFilters)) {
                foreach ($this->nullCompareFilters as $filter) {
                    $fields = [$filter->getField(), $filter->getField()];
                    $conditions = [
                        [$filter->getConditionType() => $filter->getValue()],
                        ['null' => true]
                    ];
                    $collection->addFieldToFilter($fields, $conditions);
                }
            }

            foreach ($this->helper->getOrderFields() as $key => $value) {
                if ($this->isColumnVisible($key)) {
                    $this->getColumn($key)->addField($collection);
                }
            }

            $prefix = \Amasty\Ogrid\Model\Column::TABLE_PREFIX;
            $tableName = $prefix . $collection->getTable('sales_order');

            if ($this->isColumnVisible('amasty_ogrid_sales_order_protect_code')) {
                $collection->getSelect()->columns(
                    ['amasty_ogrid_sales_order_protect_code' => $tableName . '.protect_code']
                );
            }

            if ($this->isColumnVisible('amasty_ogrid_sales_order_store_id')) {
                $collection->getSelect()->columns(
                    ['amasty_ogrid_sales_order_store_id' => $tableName . '.store_id']
                );
            }

            if (count($this->helper->getHideStatuses()) > 0) {
                $collection->addFieldToFilter('main_table.status', ['nin' => $this->helper->getHideStatuses()]);
            }

            $this->applyOrderItemFilters($collection);

        }

        if (method_exists($collection, 'isLoaded') && $collection->isLoaded()) {
            $collection->clear();
        }

        return $collection;
    }

    protected function applyOrderItemFilters($collection)
    {
        $applyFilter = false;
        $orderItemCollection = $this->getOrderItemCollection(['items' => []]);

        $this->prepareOrderItemCollection($orderItemCollection);

        foreach ($this->helper->getOrderItemFields() as $key => $value) {
            if (array_key_exists($key, $this->filterData)) {
                $applyFilter = true;
                $this->getColumn($key)->addFieldToFilter($orderItemCollection, $this->filterData[$key]);
            }
        }

        foreach ($this->helper->getAttributesFields() as $key => $attribute) {
            if (array_key_exists($attribute->getAttributeDbAlias(), $this->filterData)) {
                $applyFilter = true;
                $attribute->addFieldToFilter(
                    $orderItemCollection,
                    $this->filterData[$attribute->getAttributeDbAlias()]
                );
            }
        }

        $resource = $orderItemCollection->getResource();

        if ($applyFilter) {
            $idsSelect = new \Zend_Db_Expr(
                "select DISTINCT order_id " .
                "from (" . $orderItemCollection->getSelect()->__toString() . ") as tmp"
            );
            $collection->getSelect()->where(
                'main_table.entity_id IN (?)',
                [new \Zend_Db_Expr('(' . $idsSelect . ')')]
            );
        }
    }

    protected function getColumn($key)
    {
        if (!array_key_exists($key, $this->columns)) {
            $this->columns[$key] = \Magento\Framework\App\ObjectManager::getInstance()->create(
                'Amasty\\Ogrid\\Model\\Column\\' . $this->helper->getOrderField($key)
            );
        }

        return $this->columns[$key];
    }

    protected function getOrderItemCollection($data)
    {
        $orderItemCollection = null;
        if (array_key_exists('items', $data)) {
            $orderIds = [];
            foreach ($data['items'] as $item) {
                $orderIds[] = $item['entity_id'];
                $this->orderConfig[$item['entity_id']] = [
                    'order_currency_code' => $item['order_currency_code'],
                    'base_currency_code' => $item['base_currency_code']
                ];
            }

            $orderItemCollection = $this->orderItemCollectionFactory->create();

            if (count($orderIds) > 0) {
                $orderItemCollection
                    ->addFieldToFilter('order_id', ['in' => $orderIds]);
            }

            $orderItemCollection->getSelect()->joinLeft(
                [
                    \Amasty\Ogrid\Model\Attribute::TABLE_ALIAS => $orderItemCollection->getTable(
                        'amasty_ogrid_attribute_index'
                    )
                ],
                \Amasty\Ogrid\Model\Attribute::TABLE_ALIAS . '.order_item_id = main_table.item_id',
                []
            );

            $orderItemCollection->getSelect()->setPart(\Zend_Db_Select::COLUMNS, []);
        }

        return $orderItemCollection;
    }

    protected function getOrderShipmentTrackCollection($data)
    {
        $shipmentTrackCollection = null;
        if (array_key_exists('items', $data)) {
            $orderIds = [];
            foreach ($data['items'] as $item) {
                $orderIds[] = $item['entity_id'];
            }

            $shipmentTrackCollection = $this->shipmentTrackCollectionFactory->create();

            if (count($orderIds) > 0) {
                $shipmentTrackCollection
                    ->addFieldToFilter('order_id', ['in' => $orderIds]);
            }

            $shipmentTrackCollection->getSelect()->setPart(\Zend_Db_Select::COLUMNS, []);
        }

        return $shipmentTrackCollection;
    }

    public function prepareOrderItemCollection($orderItemCollection)
    {
        foreach ($this->helper->getOrderItemFields() as $key => $value) {
            if ($this->isColumnVisible($key)) {
                $this->getColumn($key)->addFieldToSelect($orderItemCollection);
            }
        }

        foreach ($this->helper->getAttributeCollection() as $attribute) {
            if ($this->isColumnVisible($attribute->getAttributeDbAlias())) {
                $attribute->addFieldToSelect($orderItemCollection);
            }
        }

        $orderItemCollection->getSelect()->columns(['order_id', 'item_id', 'parent_item_id']);
    }

    public function prepareOrderTrackCollection($trackCollection)
    {
        foreach ($this->helper->getTrackFields() as $key => $value) {
            if ($this->isColumnVisible($key)) {
                $this->getColumn($key)->addFieldToSelect($trackCollection);
            }
        }
        $trackCollection->join(
            ['sales_order' => $trackCollection->getTable('sales_order')],
            'sales_order.entity_id = main_table.order_id',
            ['sales_order.protect_code', 'sales_order.store_id']
        );

        $trackCollection->getSelect()->columns(['order_id', 'entity_id']);
    }

    public function modifyOrderItemData(&$orderItemData)
    {
        $orderItemField = $this->helper->getOrderItemFields();
        $attributesCollection = $this->helper->getAttributeCollection();

        $reorderedData = [];
        $childData = [];

        foreach ($orderItemData as $idx => &$orderItem) {
            $orderId = $orderItem['order_id'];
            $parentItemId = $orderItem['parent_item_id'];
            $itemId = $orderItem['item_id'];

            foreach ($orderItemField as $key => $value) {
                if ($this->isColumnVisible($key)) {
                    $this->getColumn($key)->modifyItem($orderItem, $this->orderConfig);
                }
            }

            /** @var Attribute $attribute */
            foreach ($attributesCollection as $attribute) {
                if ($this->isColumnVisible($attribute->getAttributeDbAlias())) {
                    $attribute->modifyItem($orderItem, $this->orderConfig);
                }
            }

            if (!$parentItemId) {
                $reorderedData[$orderId][$itemId] = $orderItem;
            } else {
                $childData[$parentItemId] = $orderItem;
            }
        }

        $this->moveDataFromChildToParent($reorderedData, $childData);

        $orderItemData = $reorderedData;
    }

    public function modifyOrderTrackData(&$orderTrackData)
    {
        $orderTrackField = $this->helper->getTrackFields();

        $reorderedData = [];

        foreach ($orderTrackData as $itemId => $orderTrackItem) {
            $orderId = $orderTrackItem['order_id'];
            $entityId = $orderTrackItem['entity_id'];

            foreach ($orderTrackField as $key => $value) {
                if ($this->isColumnVisible($key)) {

                    if (!array_key_exists($orderId, $reorderedData)) {
                        $reorderedData[$orderId] = [];
                    }

                    $reorderedData[$orderId][$this->getColumn($key)->getAlias()] = [
                        $entityId => $orderTrackItem[$this->getColumn($key)->getAlias()],
                        'protect_code' => $orderTrackItem['protect_code'],
                        'store_id' => $orderTrackItem['store_id']
                    ];
                }
            }
        }

        $orderTrackData = $reorderedData;
    }

    protected function moveDataFromChildToParent(&$reorderedData, $childData)
    {
        $attributesCollection = $this->helper->getAttributeCollection();

        foreach ($reorderedData as &$orderData) {
            foreach ($orderData as $orderItemId => &$orderItem) {
                if (array_key_exists($orderItemId, $childData)) {
                    $childItem = $childData[$orderItemId];
                    // First replace all possible parent order item values
                    foreach ($childItem as $column => $value) {
                        if ($this->isColumnVisible($column) && array_key_exists($column, $orderItem)) {
                            $orderItem[$column] = $value;
                        }
                    }
                    // Replace specific attribute values from index table
                    foreach ($attributesCollection as $attribute) {
                        if ($this->isColumnVisible($attribute->getAttributeDbAlias())) {
                            $value = $this->getValue($childItem, $orderItem, $attribute);
                            $orderItem[$attribute->getAttributeDbAlias()] = $value;
                        }
                    }
                }
            }
        }
    }

    public function getValue($childItem, $orderItem, $attribute)
    {
        $value = [];
        $childValue = $childItem[$attribute->getAttributeDbAlias()];
        $parentValue = $orderItem[$attribute->getAttributeDbAlias()];

        if (!is_array($parentValue) && $parentValue !== null) {
            $value = [$parentValue];
        } else {
            if (is_array($parentValue)) {
                $value = $parentValue;
            }
        }

        if (!is_array($childValue) && $childValue !== null) {
            $value = array_merge($value, [$childValue]);
        } else {
            if (is_array($childValue)) {
                $value = array_merge($value, $childValue);
            }
        }

        return $value;
    }

    public function afterGetData(
        \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider $dataProvider,
        $data
    ) {
        if ($this->isOrderGrid($dataProvider) && $data['totalRecords'] > 0) {
            $orderItemCollection = $this->getOrderItemCollection($data);
            $shipmentTrackCollection = $this->getOrderShipmentTrackCollection($data);

            $this->prepareOrderItemCollection($orderItemCollection);
            $this->prepareOrderTrackCollection($shipmentTrackCollection);

            $orderItemData = $orderItemCollection->getData();
            $shipmentTrackData = $shipmentTrackCollection->getData();

            $this->modifyOrderItemData($orderItemData);
            $this->modifyOrderTrackData($shipmentTrackData);

            $items = &$data['items'];
            foreach ($items as $idx => &$element) {
                $element['amasty_ogrid_items_ordered'] = [];
                $itemsOrdered = &$element['amasty_ogrid_items_ordered'];

                if (array_key_exists($element['entity_id'], $orderItemData)) {
                    $itemsOrdered = $orderItemData[$element['entity_id']];
                }

                if (array_key_exists($element['entity_id'], $shipmentTrackData)) {
                    $element += $shipmentTrackData[$element['entity_id']];
                }
            }
        }

        return $data;
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return FilterGroup[]
     */
    private function extractFiltersForNullCompare(SearchCriteria $searchCriteria): array
    {
        $newFilterGroups = [];

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $newFilters = [];

            foreach ($filterGroup->getFilters() as $filter) {
                if (($filter->getConditionType() == self::GREATER_THAN_CONDITION && $filter->getValue() == 0)
                    || ($filter->getConditionType() == self::LESS_THAN_CONDITION && $filter->getValue() >= 0)
                ) {
                    $this->nullCompareFilters[] = $filter;
                } else {
                    $newFilters[] = $filter;
                }
            }

            if (count($newFilters)) {
                $newFilterGroups[] = $this->filterGroupFactory->create()
                    ->setFilters($newFilters);
            }
        }

        return $newFilterGroups;
    }
}
