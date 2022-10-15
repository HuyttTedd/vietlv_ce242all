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

namespace Mageplaza\OrderAttributes\Test\Unit\Model\Plugin\Sales;

use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Select;
use Magento\Sales\Model\ResourceModel\Order;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OrderCollection;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\Plugin\Sales\Collection;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\Collection as AttributeCollection;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class TestCollection
 * @package Mageplaza\OrderAttributes\Test\Unit\Model\Plugin\Sales
 */
class TestCollection extends TestCase
{
    /**
     * @var CollectionFactory|MockObject
     */
    private $collectionFactoryMock;

    /**
     * @var Collection
     */
    private $plugin;

    /**
     * @var OrderCollection|MockObject
     */
    private $orderCollection;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->collectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->orderCollection = $this->getMockBuilder(OrderCollection::class)
            ->disableOriginalConstructor()->getMock();

        $this->plugin = new Collection($this->collectionFactoryMock);
    }

    public function testAfterGetSelect()
    {
        $result = $this->getMockBuilder(Select::class)->disableOriginalConstructor()->getMock();
        $this->orderCollection->expects($this->once())
            ->method('getFlag')
            ->with('is_mageplaza_order_attribute_sales_order_joined')
            ->willReturn(false);
        $orderResource = $this->getMockBuilder(Order::class)->disableOriginalConstructor()->getMock();
        $this->orderCollection->expects($this->once())->method('getResource')->willReturn($orderResource);
        $orderResource->expects($this->once())->method('getTable')
            ->with('mageplaza_order_attribute_sales_order')
            ->willReturn('mageplaza_order_attribute_sales_order');
        $result->expects($this->once())
            ->method('joinLeft')
            ->with(
                ['mp_order_attributes' => 'mageplaza_order_attribute_sales_order'],
                'mp_order_attributes.order_id = main_table.entity_id'
            )->willReturnSelf();
        $connection = $this->getMockBuilder(Mysql::class)->disableOriginalConstructor()->getMock();
        $this->orderCollection->expects($this->once())->method('getConnection')->willReturn($connection);
        $describeTable = [
            'my_attribute' => [
                'COLUMN_NAME' => 'my_attribute'
            ]
        ];
        $connection->expects($this->once())
            ->method('describeTable')
            ->with('mageplaza_order_attribute_sales_order')
            ->willReturn($describeTable);
        $this->orderCollection->expects($this->atLeastOnce())
            ->method('addFilterToMap')
            ->with('my_attribute', 'mp_order_attributes.my_attribute');

        $this->orderCollection->expects($this->once())
            ->method('setFlag')
            ->with('is_mageplaza_order_attribute_sales_order_joined')
            ->willReturnSelf();

        $this->plugin->afterGetSelect($this->orderCollection, $result);
    }

    public function testBeforeAddFieldToFilterWithDefaultField()
    {
        $field = 'my_attribute';
        $condition = ['eq' => 12];
        $attributeCollection = $this->getMockBuilder(AttributeCollection::class)
            ->disableOriginalConstructor()->getMock();
        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($attributeCollection);
        $attributeCollection->expects($this->once())
            ->method('addFieldToFilter')
            ->with('attribute_code', 'my_attribute')
            ->willReturnSelf();
        $attributeCollection->expects($this->once())->method('fetchItem')->willReturn([]);

        $this->assertEquals(
            [$field, $condition],
            $this->plugin->beforeAddFieldToFilter($this->orderCollection, $field, $condition)
        );
    }

    /**
     * @param string $conditionResult
     * @param string $frontendInput
     *
     * @dataProvider providerBeforeAddFieldToFilter
     */
    public function testBeforeAddFieldToFilterWithAttribute($conditionResult, $frontendInput)
    {
        $field = 'my_attribute';
        $condition = ['eq' => 12];
        $attributeCollection = $this->getMockBuilder(AttributeCollection::class)
            ->disableOriginalConstructor()->getMock();
        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($attributeCollection);
        $attributeCollection->expects($this->once())
            ->method('addFieldToFilter')
            ->with('attribute_code', $field)
            ->willReturnSelf();
        $attribute = $this->getMockBuilder(Attribute::class)->disableOriginalConstructor()->getMock();

        $attributeCollection->expects($this->once())->method('fetchItem')->willReturn($attribute);
        $attribute->expects($this->once())->method('getFrontendInput')->willReturn($frontendInput);

        $this->assertEquals(
            [$field, $conditionResult],
            $this->plugin->beforeAddFieldToFilter($this->orderCollection, $field, $condition)
        );
    }

    /**
     * @return array
     */
    public function providerBeforeAddFieldToFilter()
    {
        return [
            [['eq' => 12], 'text'],
            [['like' => '%12%'], 'multiselect'],
            [['like' => '%12%'], 'multiselect_visual']
        ];
    }
}
