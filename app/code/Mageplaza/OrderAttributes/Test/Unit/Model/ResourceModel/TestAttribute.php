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

namespace Mageplaza\OrderAttributes\Test\Unit\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute as ResourceAttribute;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

/**
 * Class TestAttribute
 * @package Mageplaza\OrderAttributes\Test\Unit\Model\ResourceModel
 */
class TestAttribute extends TestCase
{
    /**
     * Date model
     *
     * @var DateTime|MockObject
     */
    private $dateMock;

    /**
     * @var ResourceConnection|MockObject
     */
    private $resourceMock;

    /**
     * @var Attribute
     */
    private $resourceAttribute;

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()->getMock();
        $this->dateMock = $this->getMockBuilder(DateTime::class)->disableOriginalConstructor()->getMock();
        $this->resourceMock = $this->getMockBuilder(ResourceConnection::class)
            ->disableOriginalConstructor()->getMock();
        $contextMock->expects($this->once())->method('getResources')->willReturn($this->resourceMock);
        $this->resourceAttribute = new ResourceAttribute(
            $contextMock,
            $this->dateMock
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testBeforeSave()
    {
        $objectManagerHelper = new ObjectManager($this);
        $object = $objectManagerHelper->getObject(Attribute::class);
        $this->dateMock->expects($this->once())->method('date')->willReturn('04/05/2020');
        $additional = new ReflectionClass(ResourceAttribute::class);
        $method = $additional->getMethod('_beforeSave');
        $method->setAccessible(true);
        $method->invokeArgs($this->resourceAttribute, [$object]);
    }

    /**
     * @return array
     */
    public function providerTestLoadByCode()
    {
        return [
            [true, ['my_attribute' => 1]],
            [false, []],
        ];
    }

    /**
     * @param boolean $result
     * @param array $resultSelect
     *
     * @throws LocalizedException
     * @dataProvider providerTestLoadByCode
     */
    public function testLoadByCode($result, $resultSelect)
    {
        $adapterMock = $this->getMockBuilder(AdapterInterface::class)->getMock();

        $this->resourceMock->expects($this->exactly(3))
            ->method('getConnection')
            ->willReturn($adapterMock);
        $this->resourceMock->expects($this->once())
            ->method('getTableName')
            ->with('mageplaza_order_attribute', ResourceConnection::DEFAULT_CONNECTION)
            ->willReturn('mageplaza_order_attribute');

        $adapterMock->expects($this->once())->method('quoteIdentifier')
            ->with('mageplaza_order_attribute.attribute_code')
            ->willReturn('attribute_code');
        $selectMock = $this->getMockBuilder(Select::class)->disableOriginalConstructor()->getMock();
        $adapterMock->expects($this->once())->method('select')->willReturn($selectMock);
        $selectMock->expects($this->once())->method('from')->with('mageplaza_order_attribute')->willReturnSelf();
        $selectMock->expects($this->once())->method('where')->with('attribute_code=?', 'my_column')->willReturnSelf();

        $adapterMock->expects($this->once())->method('fetchRow')->with($selectMock)->willReturn($resultSelect);
        $objectManagerHelper = new ObjectManager($this);
        $attribute = $objectManagerHelper->getObject(
            Attribute::class
        );

        $this->assertEquals($result, $this->resourceAttribute->loadByCode($attribute, 'my_column'));
    }

    public function testCheckSalesOrderColumn()
    {
        $this->resourceMock->expects($this->once())
            ->method('getTableName')
            ->with('sales_order', ResourceConnection::DEFAULT_CONNECTION)
            ->willReturn('sales_order');
        $adapterMock = $this->getMockBuilder(AdapterInterface::class)->getMock();

        $this->resourceMock->expects($this->once())
            ->method('getConnection')
            ->willReturn($adapterMock);
        $adapterMock->expects($this->once())
            ->method('tableColumnExists')
            ->with('sales_order', 'my_column')
            ->willReturn(true);

        $this->assertEquals(true, $this->resourceAttribute->checkSalesOrderColumn('my_column'));
    }
}
