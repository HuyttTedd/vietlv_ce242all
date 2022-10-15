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
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class TestAbstractSales
 * @package Mageplaza\OrderAttributes\Test\Unit\Model\ResourceModel
 */
class TestAbstractSales extends TestCase
{
    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var ExtendsAbstractSales
     */
    private $abstractSales;

    /**
     * @var ResourceConnection|MockObject
     */
    private $resourceMock;

    protected function setUp()
    {
        $this->helperDataMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();

        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()->getMock();

        $this->resourceMock = $this->getMockBuilder(ResourceConnection::class)
            ->disableOriginalConstructor()->getMock();
        $contextMock->expects($this->once())->method('getResources')->willReturn($this->resourceMock);

        $this->abstractSales = new ExtendsAbstractSales(
            $contextMock,
            $this->helperDataMock
        );
    }

    /**
     * @return array
     */
    public function providerTestCreateAttribute()
    {
        return [
            ['datetime', ['type' => Table::TYPE_DATE, 'comment' => 'My Attribute']],
            ['decimal', ['type' => Table::TYPE_DECIMAL, 'length' => '12,4', 'comment' => 'My Attribute']],
            ['int', ['type' => Table::TYPE_INTEGER, 'comment' => 'My Attribute']],
            ['text', ['type' => Table::TYPE_TEXT, 'comment' => 'My Attribute']],
            ['varchar', ['type' => Table::TYPE_TEXT, 'length' => 255, 'comment' => 'My Attribute']]
        ];
    }

    /**
     * @param string $backendType
     * @param array $definition
     *
     * @throws LocalizedException
     * @dataProvider providerTestCreateAttribute
     */
    public function testCreateAttribute($backendType, $definition)
    {
        /**
         * @var Attribute|MockObject $orderAttributeMock
         */
        $orderAttributeMock = $this->getMockBuilder(Attribute::class)->disableOriginalConstructor()->getMock();
        $orderAttributeMock->expects($this->once())->method('getBackendType')->willReturn($backendType);
        $orderAttributeMock->expects($this->exactly(2))->method('getAttributeCode')->willReturn('my_attribute');

        $adapterMock = $this->getMockBuilder(AdapterInterface::class)->getMock();
        $this->resourceMock->expects($this->once())
            ->method('getTableName')
            ->with(ExtendsAbstractSales::TABLE_NAME, ResourceConnection::DEFAULT_CONNECTION)
            ->willReturn(ExtendsAbstractSales::TABLE_NAME);
        $this->resourceMock->expects($this->once())
            ->method('getConnection')
            ->willReturn($adapterMock);

        $adapterMock->expects($this->once())
            ->method('addColumn')
            ->with(ExtendsAbstractSales::TABLE_NAME, 'my_attribute', $definition)
            ->willReturnSelf();

        $this->abstractSales->createAttribute($orderAttributeMock);
    }

    /**
     * @throws LocalizedException
     */
    public function testDeleteAttribute()
    {
        $adapterMock = $this->getMockBuilder(AdapterInterface::class)->getMock();
        $this->resourceMock->expects($this->once())
            ->method('getConnection')
            ->willReturn($adapterMock);
        /**
         * @var Attribute|MockObject $orderAttributeMock
         */
        $orderAttributeMock = $this->getMockBuilder(Attribute::class)->disableOriginalConstructor()->getMock();
        $orderAttributeMock->expects($this->once())->method('getAttributeCode')->willReturn('my_attribute');
        $this->resourceMock->expects($this->once())
            ->method('getTableName')
            ->with(ExtendsAbstractSales::TABLE_NAME, ResourceConnection::DEFAULT_CONNECTION)
            ->willReturn(ExtendsAbstractSales::TABLE_NAME);
        $adapterMock->expects($this->once())
            ->method('dropColumn')
            ->with(ExtendsAbstractSales::TABLE_NAME, 'my_attribute')
            ->willReturn(true);

        $this->abstractSales->deleteAttribute($orderAttributeMock);
    }
}
