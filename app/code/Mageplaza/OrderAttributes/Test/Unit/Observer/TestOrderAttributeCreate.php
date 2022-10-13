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
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\Order as OrderAttribute;
use Mageplaza\OrderAttributes\Model\OrderFactory as OrderAttributeFactory;
use Mageplaza\OrderAttributes\Model\Quote as QuoteAttribute;
use Mageplaza\OrderAttributes\Model\QuoteFactory as QuoteAttributeFactory;
use Mageplaza\OrderAttributes\Observer\OrderAttributeCreate;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * Class TestOrderAttributeCreate
 * @package Mageplaza\OrderAttributes\Test\Unit\Observer
 */
class TestOrderAttributeCreate extends TestCase
{
    /**
     * @var OrderAttributeFactory|MockObject
     */
    private $orderAttributeFactoryMock;

    /**
     * @var QuoteAttributeFactory|MockObject
     */
    private $quoteAttributeFactoryMock;

    /**
     * @var OrderAttributeCreate
     */
    private $orderAttributeCreate;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->orderAttributeFactoryMock = $this->getMockBuilder(OrderAttributeFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->quoteAttributeFactoryMock = $this->getMockBuilder(QuoteAttributeFactory::class)
            ->disableOriginalConstructor()->getMock();

        $this->orderAttributeCreate = new OrderAttributeCreate(
            $this->quoteAttributeFactoryMock,
            $this->orderAttributeFactoryMock
        );
    }

    /**
     * @param int |null $id
     *
     * @throws LocalizedException
     * @throws ReflectionException
     * @dataProvider providerExecute
     */
    public function testExecute($id)
    {
        /**
         * @var Observer|MockObject $observerMock
         */
        $observerMock = $this->createMock(Observer::class);
        $eventMock = $this->createPartialMock(Event::class, ['getAttribute']);
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);
        $objectManagerHelper = new ObjectManager($this);
        $attribute = $objectManagerHelper->getObject(Attribute::class, ['data' => ['id' => $id]]);
        $eventMock->expects($this->once())->method('getAttribute')->willReturn($attribute);

        if (!$id) {
            $quoteAttributeMock = $this->getMockBuilder(QuoteAttribute::class)->disableOriginalConstructor()->getMock();
            $this->quoteAttributeFactoryMock->expects($this->once())->method('create')->willReturn($quoteAttributeMock);
            $quoteAttributeMock->expects($this->once())->method('createAttribute')->with($attribute);

            $orderAttributeMock = $this->getMockBuilder(OrderAttribute::class)->disableOriginalConstructor()->getMock();
            $this->orderAttributeFactoryMock->expects($this->once())->method('create')->willReturn($orderAttributeMock);
            $orderAttributeMock->expects($this->once())->method('createAttribute')->with($attribute);
        }

        $this->orderAttributeCreate->execute($observerMock);
    }

    /**
     * @return array
     */
    public function providerExecute()
    {
        return [
            [1],
            [null],
        ];
    }
}
