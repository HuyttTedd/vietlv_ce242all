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
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit\Options\Options;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class OptionsTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Block\Adminhtml\Attribute\Edit\Options
 */
class OptionsTest extends TestCase
{
    /**
     * @var Registry|MockObject
     */
    private $registryMock;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var Options
     */
    private $optionsBock;

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

        $this->optionsBock = new Options(
            $contextMock,
            $this->registryMock,
            $this->helperDataMock
        );
    }

    /**
     * @return array
     */
    public function providerGetOptionsValue()
    {
        return [
            [
                'select',
                'radio',
            ],
            [
                'select_visual',
                'radio',
            ],
            [
                'multiselect',
                'checkbox',
            ],
            [
                'multiselect_visual',
                'checkbox',
            ]
        ];
    }

    /**
     * @param string $frontendInput
     * @param string $type
     *
     * @dataProvider providerGetOptionsValue
     */
    public function testGetOptionValues($frontendInput, $type)
    {
        $storeId = 1;
        $options = '{"option":{"order":{"option_0":"1","option_1":"2"},"value":{"option_0":{"0":"Admin1","1":"Default Store View 1","3":" Us1","2":""},"option_1":{"0":"Admin2","1":"Default Store View 2","3":"Us1","2":""}},"delete":{"option_0":"","option_1":""}},"default":["option_0"]}';
        $optionsDecode = [
            'option' => [
                'order' => [
                    'option_0' => '1',
                    'option_1' => '2'
                ],
                'value' => [
                    'option_0' => [
                        'Admin1',
                        'Default Store View 1',
                        'Us1',
                        ''
                    ],
                    'option_1' => [
                        'Admin2',
                        'Default Store View 2',
                        'Us1',
                        ''
                    ]
                ],
                'delete' => [
                    'option_0' => '',
                    'option_1' => ''
                ]
            ],
            'default' => [
                'option_0'
            ]
        ];

        $result = [
            new DataObject([
                'checked' => 'checked="checked"',
                'intype' => $type,
                'sort_order' => '1',
                'id' => 'option_0',
                'store1' => 'Default Store View 1',
            ]),
            new DataObject([
                'checked' => '',
                'intype' => $type,
                'id' => 'option_1',
                'sort_order' => '2',
                'store1' => 'Default Store View 2',
            ])
        ];
        $orderAttributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('entity_attribute')
            ->willReturn($orderAttributeMock);
        $orderAttributeMock->expects($this->once())->method('getOptions')->willReturn($options);
        $this->helperDataMock->expects($this->once())->method('jsonDecodeData')
            ->with($options)
            ->willReturn($optionsDecode);
        $orderAttributeMock->expects($this->once())->method('getFrontendInput')->willReturn($frontendInput);
        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()->getMock();
        $stores = [
            $storeMock
        ];
        $this->storeManagerMock->expects($this->once())->method('getStores')->willReturn($stores);
        $storeMock->expects($this->atLeastOnce())->method('getId')->willReturn($storeId);

        $this->assertEquals($result, $this->optionsBock->getOptionValues());
    }

    /**
     * @return array
     */
    public function providerTestGetStoresSortedBySortOrder()
    {
        return [
            [
                ['storeMock2', 'storeMock1', 'storeMock3'],
                ['id' => 1, 'sort_order' => 0],
                ['id' => 3, 'sort_order' => 0],
                ['id' => 2, 'sort_order' => 1],
            ],
            [
                ['storeMock2', 'storeMock3', 'storeMock1'],
                ['id' => 1, 'sort_order' => 0],
                ['id' => 3, 'sort_order' => 0],
                ['id' => 2, 'sort_order' => 0],
            ],
            [
                ['storeMock1', 'storeMock2', 'storeMock3'],
                ['id' => 1, 'sort_order' => 1],
                ['id' => 2, 'sort_order' => 2],
                ['id' => 3, 'sort_order' => 3],
            ]
        ];
    }

    /**
     * @param array $result
     * @param array $storeData1
     * @param array $storeData2
     * @param array $storeData3
     *
     * @dataProvider providerTestGetStoresSortedBySortOrder
     */
    public function testGetStoresSortedBySortOrder($result, $storeData1, $storeData2, $storeData3)
    {
        $objectManagerHelper = new ObjectManager($this);
        $storeMock1 = $objectManagerHelper->getObject(Store::class, ['data' => $storeData1]);
        $storeMock2 = $objectManagerHelper->getObject(Store::class, ['data' => $storeData2]);
        $storeMock3 = $objectManagerHelper->getObject(Store::class, ['data' => $storeData3]);

        $this->storeManagerMock->expects($this->once())
            ->method('getStores')
            ->willReturn([$storeMock1, $storeMock2, $storeMock3]);

        $this->assertEquals(
            [
                ${$result[0]},
                ${$result[1]},
                ${$result[2]}
            ],
            $this->optionsBock->getStoresSortedBySortOrder()
        );
    }
}
