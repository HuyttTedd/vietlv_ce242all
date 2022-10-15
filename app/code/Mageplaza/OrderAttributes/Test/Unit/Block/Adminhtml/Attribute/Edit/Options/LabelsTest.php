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

namespace Mageplaza\OrderAttributes\Test\Unit\Block\Adminhtml\Attribute\Edit\Options;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit\Options\Labels;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class LabelsTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Block\Adminhtml\Attribute\Edit\Options
 */
class LabelsTest extends TestCase
{
    /**
     * @var Registry|MockObject
     */
    private $registryMock;

    /**
     * @var Labels
     */
    private $labelBlock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()->getMock();
        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)->getMock();
        $contextMock->method('getStoreManager')->willReturn($this->storeManagerMock);
        $this->registryMock = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()->getMock();
        $this->helperDataMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();

        $this->labelBlock = new Labels(
            $contextMock,
            $this->registryMock,
            $this->helperDataMock
        );
    }

    public function testGetStores()
    {
        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()->getMock();
        $stores = [
            $storeMock
        ];

        $this->storeManagerMock->expects($this->once())->method('getStores')->willReturn($stores);

        $this->assertEquals($stores, $this->labelBlock->getStores());
    }

    /**
     * @return array
     */
    public function providerTestGetLabelValues()
    {
        return [
            [
                [0 => 'My Label', 1 => 'Store 1'],
                '{"0":"Default","1":"Store 1","2":"Store 2"}',
                [
                    0 => 'Default',
                    1 => 'Store 1',
                    2 => 'Store 2'
                ],
                1
            ],

            [
                [0 => 'My Label', 3 => ''],
                '{"0":"Default","1":"Store 1","2":"Store 2"}',
                [
                    0 => 'Default',
                    1 => 'Store 1',
                    2 => 'Store 2'
                ],
                3
            ],

        ];
    }

    /**
     * @param array $result
     * @param string $labels
     * @param array $labelDecode
     * @param int $storeId
     *
     * @dataProvider providerTestGetLabelValues
     */
    public function testGetLabelValues($result, $labels, $labelDecode, $storeId)
    {
        $orderAttributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $orderAttributeMock->expects($this->once())->method('getFrontendLabel')->willReturn('My Label');
        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('entity_attribute')
            ->willReturn($orderAttributeMock);

        $orderAttributeMock->expects($this->atLeastOnce())
            ->method('getLabels')
            ->willReturn($labels);
        if ($labels) {
            $this->helperDataMock->expects($this->once())
                ->method('jsonDecodeData')
                ->with($labels)
                ->willReturn($labelDecode);
        }

        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()->getMock();
        $stores = [
            $storeMock
        ];
        $this->storeManagerMock->expects($this->once())->method('getStores')->willReturn($stores);
        $storeMock->expects($this->once())->method('getId')->willReturn($storeId);

        $this->assertEquals($result, $this->labelBlock->getLabelValues());
    }
}
