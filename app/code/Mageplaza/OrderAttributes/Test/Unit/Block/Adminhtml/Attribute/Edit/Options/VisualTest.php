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
use Magento\Swatches\Helper\Media;
use Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit\Options\Visual;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class VisualTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Block\Adminhtml\Attribute\Edit\Options
 */
class VisualTest extends TestCase
{
    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var Registry|MockObject
     */
    private $registryMock;

    /**
     * @var MockObject
     */
    private $swatchHelperMock;

    /**
     * @var Visual
     */
    private $visualBlock;

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
        $this->swatchHelperMock = $this->getMockBuilder(Media::class)
            ->disableOriginalConstructor()->getMock();

        $objectManagerHelper = new ObjectManager($this);
        $this->visualBlock = $objectManagerHelper->getObject(
            Visual::class,
            [
                'context' => $contextMock,
                'registry' => $this->registryMock,
                'helperData' => $this->helperDataMock,
                'swatchHelper' => $this->swatchHelperMock
            ]
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
        $mediaUrl = 'http://myhost.com/pub/media/attribute/swatch';
        $storeId = 1;
        $options = '{"optionvisual":{"order":{"option_0":"1","option_1":"2"},"value":{"option_0":["Admin option 1 Label","Default Store View Label",""],"option_1":["Admin Option 2 Label","Default Store View Label 2",""]},"delete":{"option_0":"","option_1":""}},"defaultvisual":["option_0"]}';
        $additionalData = '{"option_0":{"swatch_value":"\/8\/1\/test.jpg","swatch_type":2},"option_1":{"swatch_value":"#b81fb8","swatch_type":1}}';
        $optionsDecode = [
            'optionvisual' => [
                'order' => [
                    'option_0' => 1,
                    'option_1' => 2
                ],
                'value' => [
                    'option_0' => [
                        0 => 'Admin option 1 Label',
                        1 => 'Default Store View Label',
                        2 => ''
                    ],

                    'option_1' => [
                        0 => 'Admin Option 2 Label',
                        1 => 'Default Store View Label 2',
                        2 => ''
                    ]
                ],

                'delete' => [
                    'option_0' => '',
                    'option_1' => ''
                ]
            ],
            'defaultvisual' => [
                'option_0'
            ]
        ];

        $additionalDataDecode = [
            'option_0' => [
                'swatch_value' => '/8/1/test.jpg',
                'swatch_type' => 2
            ],

            'option_1' => [
                'swatch_value' => '#b81fb8',
                'swatch_type' => 1
            ]
        ];

        $result = [
            new DataObject([
                'checked' => 'checked="checked"',
                'intype' => $type,
                'sort_order' => '1',
                'id' => 'option_0',
                'store1' => 'Default Store View Label',
                'swatch1' => 'background: url(' . $mediaUrl . '/8/1/test.jpg); background-size: cover;',
                'defaultswatch1' => '/8/1/test.jpg'
            ]),
            new DataObject([
                'checked' => '',
                'intype' => $type,
                'id' => 'option_1',
                'sort_order' => '2',
                'store1' => 'Default Store View Label 2',
                'swatch1' => 'background-color: #b81fb8',
                'defaultswatch1' => '#b81fb8'
            ])
        ];

        $this->swatchHelperMock->method('getSwatchMediaUrl')
            ->willReturn($mediaUrl);
        $orderAttributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('entity_attribute')
            ->willReturn($orderAttributeMock);
        $orderAttributeMock->expects($this->once())->method('getOptions')->willReturn($options);
        $this->helperDataMock->expects($this->at(0))->method('jsonDecodeData')
            ->with($options)
            ->willReturn($optionsDecode);
        $orderAttributeMock->expects($this->once())->method('getAdditionalData')->willReturn($additionalData);
        $this->helperDataMock->expects($this->at(1))->method('jsonDecodeData')
            ->with($additionalData)
            ->willReturn($additionalDataDecode);

        $orderAttributeMock->expects($this->once())->method('getFrontendInput')->willReturn($frontendInput);
        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()->getMock();
        $stores = [
            $storeMock
        ];
        $this->storeManagerMock->expects($this->once())->method('getStores')->willReturn($stores);
        $storeMock->expects($this->atLeastOnce())->method('getId')->willReturn($storeId);

        $this->assertEquals($result, $this->visualBlock->getOptionValues());
    }
}
