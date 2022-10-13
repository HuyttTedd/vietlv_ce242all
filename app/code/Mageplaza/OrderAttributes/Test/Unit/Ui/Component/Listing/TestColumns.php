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

namespace Mageplaza\OrderAttributes\Test\Unit\Ui\Component\Listing;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponent\Processor;
use Magento\Framework\View\Element\UiComponentInterface;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\Collection;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\CollectionFactory;
use Mageplaza\OrderAttributes\Ui\Component\ColumnFactory;
use Mageplaza\OrderAttributes\Ui\Component\Listing\Columns;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class TestColumns
 * @package Mageplaza\OrderAttributes\Test\Unit\Ui\Component\Listing
 */
class TestColumns extends TestCase
{
    /**
     * @var Columns
     */
    private $columns;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var CollectionFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionFactoryMock;

    /** @var  Collection|PHPUnit_Framework_MockObject_MockObject */
    private $collectionMock;

    /**
     * @var ColumnFactory|MockObject
     */
    private $columnFactoryMock;

    /**
     * @var ContextInterface|MockObject
     */
    private $contextMock;

    protected function setUp()
    {
        $this->contextMock = $this->getMockBuilder(ContextInterface::class)->getMockForAbstractClass();
        $this->helperDataMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();
        $this->columnFactoryMock = $this->getMockBuilder(ColumnFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->collectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();
        $this->collectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->columns = new Columns(
            $this->contextMock,
            $this->helperDataMock,
            $this->columnFactoryMock,
            $this->collectionFactoryMock
        );
    }

    public function testPrepareWithDisableModule()
    {
        $this->helperDataMock->expects($this->once())->method('isEnabled')->willReturn(false);
        $processor = $this->getMockBuilder(Processor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock->expects($this->atLeastOnce())->method('getProcessor')->willReturn($processor);
        $this->columns->prepare();
    }

    /**
     * @param string $frontendInput
     * @param string $filter
     *
     * @dataProvider providerPrepare
     */
    public function testPrepare($frontendInput, $filter)
    {
        $this->helperDataMock->expects($this->once())->method('isEnabled')->willReturn(true);

        $attribute = $this->getMockBuilder(Attribute::class)->disableOriginalConstructor()->getMock();
        $attribute->expects($this->once())->method('getIsUsedInGrid')->willReturn(1);
        $attribute->expects($this->once())->method('getFrontendInput')->willReturn($frontendInput);
        $attribute->expects($this->exactly(2))->method('getAttributeCode')->willReturn('my_attribute');
        $config = [
            'sortOrder' => Columns::DEFAULT_COLUMNS_MAX_ORDER + 1,
            'filter' => $filter
        ];

        $column = $this->getMockBuilder(UiComponentInterface::class)->getMockForAbstractClass();
        $column->expects($this->once())->method('prepare');
        $this->columnFactoryMock->expects($this->once())
            ->method('create')
            ->with($attribute, $this->contextMock, $config)
            ->willReturn($column);

        $this->collectionFactoryMock->method('create')->willReturn([$attribute]);

        $processor = $this->getMockBuilder(Processor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock->expects($this->atLeastOnce())->method('getProcessor')->willReturn($processor);
        $this->columns->prepare();
    }

    /**
     * @return array
     */
    public function providerPrepare()
    {
        return [
            ['default', 'text'],
            ['boolean', 'select'],
            ['select', 'select'],
            ['select_visual', 'select'],
            ['multiselect', 'select'],
            ['multiselect_visual', 'select'],
            ['date', 'dateRange']
        ];
    }
}
