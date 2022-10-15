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
use Magento\Ui\Model\ResourceModel\Bookmark\Collection;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\Order as OrderAttribute;
use Mageplaza\OrderAttributes\Model\OrderFactory as OrderAttributeFactory;
use Mageplaza\OrderAttributes\Model\Quote as QuoteAttribute;
use Mageplaza\OrderAttributes\Model\QuoteFactory as QuoteAttributeFactory;
use Mageplaza\OrderAttributes\Observer\OrderAttributeDelete;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * Class TestOrderAttributeDelete
 * @package Mageplaza\OrderAttributes\Test\Unit\Observer
 */
class TestOrderAttributeDelete extends TestCase
{
    /**
     * @var OrderAttributeFactory|MockObject
     */
    protected $orderAttributeFactoryMock;

    /**
     * @var QuoteAttributeFactory|MockObject
     */
    protected $quoteAttributeFactoryMock;

    /**
     * @var OrderAttributeDelete
     */
    protected $orderAttributeDelete;

    /**
     * @var Collection|MockObject
     */
    private $uiBookmarkCollection;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->orderAttributeFactoryMock = $this->getMockBuilder(OrderAttributeFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->quoteAttributeFactoryMock = $this->getMockBuilder(QuoteAttributeFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->uiBookmarkCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();

        $this->orderAttributeDelete = new OrderAttributeDelete(
            $this->quoteAttributeFactoryMock,
            $this->orderAttributeFactoryMock,
            $this->uiBookmarkCollection
        );
    }

    /**
     * @param int |null $id
     * @param int $isUsedInGrid
     *
     * @throws LocalizedException
     * @throws ReflectionException
     * @dataProvider providerExecute
     */
    public function testExecute($id, $isUsedInGrid)
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
        $attribute = $objectManagerHelper->getObject(
            Attribute::class,
            [
                'data' => ['id' => $id, 'is_used_in_grid' => $isUsedInGrid]
            ]
        );
        $eventMock->expects($this->once())->method('getAttribute')->willReturn($attribute);

        if ($id) {
            $quoteAttributeMock = $this->getMockBuilder(QuoteAttribute::class)->disableOriginalConstructor()->getMock();
            $this->quoteAttributeFactoryMock->expects($this->once())->method('create')->willReturn($quoteAttributeMock);
            $quoteAttributeMock->expects($this->once())->method('deleteAttribute')->with($attribute);

            $orderAttributeMock = $this->getMockBuilder(OrderAttribute::class)->disableOriginalConstructor()->getMock();
            $this->orderAttributeFactoryMock->expects($this->once())->method('create')->willReturn($orderAttributeMock);
            $orderAttributeMock->expects($this->once())->method('deleteAttribute')->with($attribute);

            if ($isUsedInGrid) {
                $this->uiBookmarkCollection->expects($this->once())
                    ->method('addFieldToFilter')
                    ->with('namespace', 'sales_order_grid')
                    ->willReturnSelf();
                $this->uiBookmarkCollection->expects($this->once())
                    ->method('walk')
                    ->with('delete');
            }
        }

        $this->orderAttributeDelete->execute($observerMock);
    }

    /**
     * @return array
     */
    public function providerExecute()
    {
        return [
            [1, 1],
            [1, 0],
            [null, 0]
        ];
    }
}
