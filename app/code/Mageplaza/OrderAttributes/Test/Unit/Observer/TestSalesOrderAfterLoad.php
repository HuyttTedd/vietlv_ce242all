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

namespace Mageplaza\OrderAttributes\Test\Unit\Observer;

use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Order as OrderAttribute;
use Mageplaza\OrderAttributes\Model\OrderFactory as OrderAttributeFactory;
use Mageplaza\OrderAttributes\Observer\SalesOrderAfterLoad;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * Class TestSalesOrderAfterLoad
 * @package Mageplaza\OrderAttributes\Test\Unit\Observer
 */
class TestSalesOrderAfterLoad extends TestCase
{
    /**
     * @var OrderAttributeFactory|MockObject
     */
    protected $orderAttributeFactoryMock;

    /**
     * @var Data|MockObject
     */
    protected $helperDataMock;

    /**
     * @var SalesOrderAfterLoad
     */
    protected $salesOrderLoadAfter;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->helperDataMock = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $this->orderAttributeFactoryMock = $this->getMockBuilder(OrderAttributeFactory::class)
            ->disableOriginalConstructor()->getMock();

        $this->salesOrderLoadAfter = new SalesOrderAfterLoad($this->orderAttributeFactoryMock, $this->helperDataMock);
    }

    /**
     * @param boolean $isEnable
     * @param int|string $quoteAttributeId
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws ReflectionException
     * @dataProvider providerExecute
     */
    public function testExecute($isEnable, $quoteAttributeId)
    {
        /**
         * @var Observer|MockObject $observerMock
         */
        $observerMock = $this->createMock(Observer::class);
        $eventMock = $this->createPartialMock(Event::class, ['getOrder']);
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);
        $order = $this->getMockBuilder(Order::class)->disableOriginalConstructor()->getMock();
        $eventMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);

        $storeId = 1;
        $order->expects($this->atLeastOnce())->method('getStoreId')->willReturn($storeId);
        $this->helperDataMock->expects($this->once())->method('isEnabled')->with($storeId)->willReturn($isEnable);
        if ($isEnable) {
            $orderAttributeMock = $this->getMockBuilder(OrderAttribute::class)->disableOriginalConstructor()->getMock();
            $this->orderAttributeFactoryMock->expects($this->once())->method('create')->willReturn($orderAttributeMock);
            $order->expects($this->once())->method('getId')->willReturn(1);

            $orderAttributeMock->expects($this->once())->method('load')->willReturnSelf();
            $orderAttributeMock->expects($this->once())->method('getId')->willReturn($quoteAttributeId);

            if ($quoteAttributeId) {
                $quoteAttributeData = ['my_attribute' => 'test'];
                $result = ['my_attribute_label' => 'Test', 'my_attribute' => 'test'];
                $orderAttributeMock->expects($this->once())
                    ->method('getData')
                    ->willReturn($quoteAttributeData);

                $this->helperDataMock->expects($this->once())
                    ->method('prepareAttributes')
                    ->with($storeId, $quoteAttributeData)
                    ->willReturn($result);

                $order->expects($this->once())->method('addData')->with($result);
            }
        }

        $this->salesOrderLoadAfter->execute($observerMock);
    }

    /**
     * @return array
     */
    public function providerExecute()
    {
        return [
            [true, 1],
            [true, ''],
            [false, 1]
        ];
    }
}
