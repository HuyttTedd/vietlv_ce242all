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

use Exception;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\Order as OrderAttribute;
use Mageplaza\OrderAttributes\Model\OrderFactory as OrderAttributeFactory;
use Mageplaza\OrderAttributes\Model\Quote as QuoteAttribute;
use Mageplaza\OrderAttributes\Model\QuoteFactory as QuoteAttributeFactory;
use Mageplaza\OrderAttributes\Observer\QuoteSubmitSuccess;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionException;

/**
 * Class TestQuoteSubmitSuccess
 * @package Mageplaza\OrderAttributes\Test\Unit\Observer
 */
class TestQuoteSubmitSuccess extends TestCase
{
    /**
     * @var QuoteAttributeFactory|MockObject
     */
    private $quoteAttributeFactoryMock;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var OrderAttributeFactory|MockObject
     */
    private $orderAttributeFactoryMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * @var QuoteAttribute|MockObject
     */
    private $quoteAttributeMock;

    /**
     * @var OrderAttribute|MockObject
     */
    private $orderAttributeMock;

    /**
     * @var Order|MockObject
     */
    private $orderMock;

    /**
     * @var QuoteSubmitSuccess
     */
    private $quoteSubmitSuccess;

    protected function setup()
    {
        $this->quoteAttributeFactoryMock = $this->getMockBuilder(QuoteAttributeFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->orderAttributeFactoryMock = $this->getMockBuilder(OrderAttributeFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->helperDataMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();
        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->getMockForAbstractClass();

        $this->quoteSubmitSuccess = new QuoteSubmitSuccess(
            $this->quoteAttributeFactoryMock,
            $this->orderAttributeFactoryMock,
            $this->loggerMock,
            $this->helperDataMock
        );
    }

    public function testExecuteWithDisableModule()
    {
        $storeId = 1;

        /**
         * @var Observer|MockObject $observerMock
         */
        $observerMock = $this->createMock(Observer::class);
        $eventMock = $this->createPartialMock(Event::class, ['getOrder']);
        $this->orderMock = $this->getMockBuilder(Order::class)->disableOriginalConstructor()->getMock();
        $eventMock->expects($this->once())->method('getOrder')->willReturn($this->orderMock);
        $this->orderMock->expects($this->once())->method('getStoreId')->willReturn($storeId);
        $this->helperDataMock->expects($this->once())->method('isEnabled')->with($storeId)->willReturn(false);
        $observerMock->expects($this->atLeastOnce())->method('getEvent')->willReturn($eventMock);

        $this->quoteSubmitSuccess->execute($observerMock);
    }

    public function testExecuteWithQuoteAttributeEmpty()
    {
        $storeId = 1;

        /**
         * @var Observer $observerMock
         */
        $observerMock = $this->createMock(Observer::class);
        $eventMock = $this->createPartialMock(Event::class, ['getOrder']);
        $this->orderMock = $this->getMockBuilder(Order::class)->disableOriginalConstructor()->getMock();
        $eventMock->expects($this->once())->method('getOrder')->willReturn($this->orderMock);
        $this->orderMock->expects($this->once())->method('getStoreId')->willReturn($storeId);
        $this->helperDataMock->expects($this->once())->method('isEnabled')->with($storeId)->willReturn(true);
        $observerMock->expects($this->atLeastOnce())->method('getEvent')->willReturn($eventMock);

        $this->quoteAttributeMock = $this->getMockBuilder(QuoteAttribute::class)
            ->disableOriginalConstructor()->getMock();

        $this->orderAttributeMock = $this->getMockBuilder(OrderAttribute::class)
            ->disableOriginalConstructor()->getMock();

        $this->quoteAttributeFactoryMock->expects($this->once())
            ->method('create')->willReturn($this->quoteAttributeMock);
        $quoteId = 1;
        $this->orderMock->expects($this->once())->method('getQuoteId')->willReturn($quoteId);
        $this->quoteAttributeMock->expects($this->once())->method('load')->with($quoteId)->willReturnSelf();

        $orderId = 1;
        $this->orderMock->expects($this->once())->method('getId')->willReturn($orderId);
        $this->orderAttributeMock->expects($this->once())->method('load')->with($orderId)->willReturnSelf();

        $this->orderAttributeFactoryMock->expects($this->once())
            ->method('create')->willReturn($this->orderAttributeMock);

        $this->quoteAttributeMock->expects($this->once())->method('getData')->willReturn(null);

        $this->orderAttributeMock->expects($this->once())->method('getData')->willReturn(null);

        $this->quoteSubmitSuccess->execute($observerMock);
    }

    public function testExecuteWithoutFileAttribute()
    {
        $storeId = 1;

        /**
         * @var Observer $observerMock
         */
        $observerMock = $this->createMock(Observer::class);
        $eventMock = $this->createPartialMock(Event::class, ['getOrder']);
        $this->orderMock = $this->getMockBuilder(Order::class)->disableOriginalConstructor()->getMock();
        $eventMock->expects($this->once())->method('getOrder')->willReturn($this->orderMock);
        $this->orderMock->expects($this->once())->method('getStoreId')->willReturn($storeId);
        $this->helperDataMock->expects($this->once())->method('isEnabled')->with($storeId)->willReturn(true);
        $observerMock->expects($this->atLeastOnce())->method('getEvent')->willReturn($eventMock);

        $this->quoteAttributeMock = $this->getMockBuilder(QuoteAttribute::class)
            ->disableOriginalConstructor()->getMock();

        $this->orderAttributeMock = $this->getMockBuilder(OrderAttribute::class)
            ->disableOriginalConstructor()->getMock();

        $this->quoteAttributeFactoryMock->expects($this->once())
            ->method('create')->willReturn($this->quoteAttributeMock);
        $quoteId = 1;
        $this->orderMock->expects($this->once())->method('getQuoteId')->willReturn($quoteId);
        $this->quoteAttributeMock->expects($this->once())->method('load')->with($quoteId)->willReturnSelf();

        $orderId = 1;
        $this->orderMock->expects($this->exactly(2))->method('getId')->willReturn($orderId);
        $this->orderAttributeMock->expects($this->once())->method('load')->with($orderId)->willReturnSelf();

        $this->orderAttributeFactoryMock->expects($this->once())
            ->method('create')->willReturn($this->orderAttributeMock);

        $quoteAttributeData = ['my_attribute' => 'Test'];
        $this->quoteAttributeMock->expects($this->once())->method('getData')->willReturn($quoteAttributeData);

        $this->orderAttributeMock->expects($this->once())->method('getData')->willReturn(null);

        $customerGroupId = 1;
        $filters = ['frontend_input' => ['in' => ['image', 'file']]];
        $this->orderMock->expects($this->once())->method('getCustomerGroupId')->willReturn($customerGroupId);
        $this->helperDataMock->expects($this->once())->method('getOrderAttributesCollection')
            ->with($storeId, $customerGroupId, false, $filters)->willReturn([]);

        $this->orderAttributeMock->expects($this->once())->method('saveAttributeData')
            ->with($orderId)->willReturn($quoteAttributeData);

        $this->quoteSubmitSuccess->execute($observerMock);
    }

    /**
     * Test for default and file attribute. Image attribute is the same file attribute
     *
     * @param boolean $isVisible
     * @param array $quoteAttributeData
     * @param boolean $isThrowException
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws ReflectionException
     * @dataProvider providertestExecute
     */
    public function testExecute($isVisible, $quoteAttributeData, $isThrowException = false)
    {
        $storeId = 1;

        /**
         * @var Observer|MockObject $observerMock
         */
        $observerMock = $this->createMock(Observer::class);
        $eventMock = $this->createPartialMock(Event::class, ['getOrder']);
        $this->orderMock = $this->getMockBuilder(Order::class)->disableOriginalConstructor()->getMock();
        $eventMock->expects($this->once())->method('getOrder')->willReturn($this->orderMock);
        $this->orderMock->expects($this->once())->method('getStoreId')->willReturn($storeId);
        $this->helperDataMock->expects($this->once())->method('isEnabled')->with($storeId)->willReturn(true);
        $observerMock->expects($this->atLeastOnce())->method('getEvent')->willReturn($eventMock);

        $this->quoteAttributeMock = $this->getMockBuilder(QuoteAttribute::class)
            ->disableOriginalConstructor()->getMock();

        $this->orderAttributeMock = $this->getMockBuilder(OrderAttribute::class)
            ->disableOriginalConstructor()->getMock();

        $this->quoteAttributeFactoryMock->expects($this->once())
            ->method('create')->willReturn($this->quoteAttributeMock);
        $quoteId = 1;
        $this->orderMock->expects($this->once())->method('getQuoteId')->willReturn($quoteId);
        $this->quoteAttributeMock->expects($this->once())->method('load')->with($quoteId)->willReturnSelf();

        $orderId = 1;
        $this->orderMock->expects($this->exactly(2))->method('getId')->willReturn($orderId);
        $this->orderAttributeMock->expects($this->once())->method('load')->with($orderId)->willReturnSelf();

        $this->orderAttributeFactoryMock->expects($this->once())
            ->method('create')->willReturn($this->orderAttributeMock);

        $this->quoteAttributeMock->expects($this->once())->method('getData')->willReturn($quoteAttributeData);

        $this->orderAttributeMock->expects($this->once())->method('getData')->willReturn(null);

        $customerGroupId = 1;
        $filters = ['frontend_input' => ['in' => ['image', 'file']]];
        $this->orderMock->expects($this->atLeastOnce())->method('getCustomerGroupId')->willReturn($customerGroupId);

        $attribute = $this->getMockBuilder(Attribute::class)->disableOriginalConstructor()->getMock();
        $attributes = [$attribute];
        $this->helperDataMock->expects($this->once())->method('getOrderAttributesCollection')
            ->with($storeId, $customerGroupId, false, $filters)->willReturn($attributes);
        $this->helperDataMock->expects($this->atLeastOnce())
            ->method('isVisible')
            ->with($attribute, $storeId, $customerGroupId)
            ->willReturn($isVisible);

        if ($isVisible) {
            $attributeCode = 'my_file';
            $file = [
                'type' => 'application/zip',
                'error' => '',
                'size' => 136852,
                'name' => 'test.zip',
                'file' => '/d/b/test.zip',
                'url' => 'https://mydomain.com/pub/media/mageplaza/order_attributes/tmp/d/b//d/b/test.zip'
            ];
            $result = $file['file'];
            $attributeName = $file['name'];
            $url = 'https://mydomain.com/mporderattributes/viewfile/index/image/L2QvYi9kYjJhNDNiNDBmNTVhMzAxN2VhNmRkZmRkNmI1N2VmZl8xXzEucG5n/';
            $attribute->expects($this->atLeastOnce())->method('getAttributeCode')->willReturn($attributeCode);
            $this->helperDataMock->expects($this->once())
                ->method('jsonDecodeData')
                ->with($quoteAttributeData[$attributeCode])
                ->willReturn($file);

            if (!$isThrowException) {
                $this->helperDataMock->expects($this->once())
                    ->method('moveTemporaryFile')
                    ->with($file)
                    ->willReturn($result);

                $this->helperDataMock->expects($this->once())
                    ->method('prepareFileName')
                    ->with($result)
                    ->willReturn($attributeName);

                $attribute->expects($this->once())
                    ->method('getFrontendInput')
                    ->willReturn('file');
                $this->helperDataMock->expects($this->once())
                    ->method('prepareFileValue')
                    ->with('file', $result)
                    ->willReturn($url);

                $this->orderMock->expects($this->exactly(2))
                    ->method('setData')
                    ->withConsecutive(
                        [$attributeCode . '_name', $attributeName],
                        [$attributeCode . '_url', $url]
                    )->willReturnSelf();

                $quoteAttributeData[$attributeCode] = $result;
            } else {
                $exception = new Exception('Exception');
                $this->helperDataMock->expects($this->once())
                    ->method('moveTemporaryFile')
                    ->with($file)
                    ->willThrowException($exception);

                $this->loggerMock->expects($this->once())
                    ->method('critical')
                    ->with($exception)
                    ->willReturnSelf();
            }
        }

        $this->orderAttributeMock->expects($this->once())
            ->method('saveAttributeData')
            ->with($orderId)
            ->willReturn($quoteAttributeData);

        $this->quoteSubmitSuccess->execute($observerMock);
    }

    /**
     * Only test for file attribute. Image is the same
     * @return array
     */
    public function providertestExecute()
    {
        $fileJson = '{"type":"application\/zip","error":"","size":136852,"name":"test.zip","file":"\/d\/b\/test.zip","url":"https:\/\/mydomain.com\/pub\/media\/mageplaza\/order_attributes\/tmp\/d\/b\/\/d\/b\/test.zip"}';

        return [
            [false, ['my_attribute' => 'Test']],
            [true, ['my_attribute' => 'Test', 'my_file' => $fileJson]],
            [true, ['my_attribute' => 'Test', 'my_file' => $fileJson], true]
        ];
    }
}
