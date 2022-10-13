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
use Magento\Quote\Model\Quote;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Quote as QuoteAttribute;
use Mageplaza\OrderAttributes\Model\QuoteFactory as QuoteAttributeFactory;
use Mageplaza\OrderAttributes\Observer\SalesQuoteAfterLoad;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * Class TestSalesQuoteAfterLoad
 * @package Mageplaza\OrderAttributes\Test\Unit\Observer
 */
class TestSalesQuoteAfterLoad extends TestCase
{
    /**
     * @var QuoteAttributeFactory|MockObject
     */
    protected $quoteAttributeFactoryMock;

    /**
     * @var Data|MockObject
     */
    protected $helperDataMock;

    /**
     * @var SalesQuoteAfterLoad
     */
    protected $salesQuoteLoadAfter;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->helperDataMock = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $this->quoteAttributeFactoryMock = $this->getMockBuilder(QuoteAttributeFactory::class)
            ->disableOriginalConstructor()->getMock();

        $this->salesQuoteLoadAfter = new SalesQuoteAfterLoad($this->quoteAttributeFactoryMock, $this->helperDataMock);
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
        $eventMock = $this->createPartialMock(Event::class, ['getQuote']);
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);
        $quote = $this->getMockBuilder(Quote::class)->disableOriginalConstructor()->getMock();
        $eventMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($quote);

        $storeId = 1;
        $quote->expects($this->atLeastOnce())->method('getStoreId')->willReturn($storeId);
        $this->helperDataMock->expects($this->once())->method('isEnabled')->with($storeId)->willReturn($isEnable);
        if ($isEnable) {
            $quoteAttributeMock = $this->getMockBuilder(QuoteAttribute::class)->disableOriginalConstructor()->getMock();
            $this->quoteAttributeFactoryMock->expects($this->once())->method('create')->willReturn($quoteAttributeMock);
            $quote->expects($this->once())->method('getId')->willReturn(1);

            $quoteAttributeMock->expects($this->once())->method('load')->willReturnSelf();
            $quoteAttributeMock->expects($this->once())->method('getId')->willReturn($quoteAttributeId);

            if ($quoteAttributeId) {
                $quoteAttributeData = ['my_attribute' => 'test'];
                $result = ['my_attribute_label' => 'Test', 'my_attribute' => 'test'];
                $quoteAttributeMock->expects($this->once())
                    ->method('getData')
                    ->willReturn($quoteAttributeData);

                $this->helperDataMock->expects($this->once())
                    ->method('prepareAttributes')
                    ->with($storeId, $quoteAttributeData)
                    ->willReturn($result);

                $quote->expects($this->once())->method('addData')->with($result);
            }
        }

        $this->salesQuoteLoadAfter->execute($observerMock);
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
