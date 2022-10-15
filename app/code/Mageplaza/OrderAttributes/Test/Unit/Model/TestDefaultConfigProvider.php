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

namespace Mageplaza\OrderAttributes\Test\Unit\Model;

use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\DefaultConfigProvider;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class TestDefaultConfigProvider
 * @package Mageplaza\OrderAttributes\Test\Unit\Model
 */
class TestDefaultConfigProvider extends TestCase
{
    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var CollectionFactory|MockObject
     */
    private $collectionFactoryMock;

    /**
     * @var DefaultConfigProvider
     */
    private $defaultConfigProvider;

    protected function setUp()
    {
        $this->helperDataMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();
        $this->collectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()->getMock();

        $this->defaultConfigProvider = new DefaultConfigProvider(
            $this->collectionFactoryMock,
            $this->helperDataMock
        );
    }

    public function testGetConfigWithDisabledModule()
    {
        $this->helperDataMock->expects($this->once())->method('isEnabled')->willReturn(false);

        $this->assertEquals([], $this->defaultConfigProvider->getConfig());
    }

    /**
     * @param array $attributeData
     */

    /**
     * @param array $result
     * @param string $frontendInput
     * @param string $fieldDepend
     * @param string $shippingDepend
     * @param array $attributeData
     *
     * @dataProvider providerGetConfig
     */
    public function testGetConfig($result, $frontendInput, $fieldDepend, $shippingDepend, $attributeData)
    {
        $this->helperDataMock->expects($this->once())->method('isEnabled')->willReturn(true);
        $this->helperDataMock->expects($this->once())->method('isOscPage')->willReturn($result['isOscPage']);
        $this->helperDataMock->expects($this->once())
            ->method('getTinymceConfig')
            ->willReturn($result['tinymceConfig']);

        $attribute = $this->getMockBuilder(Attribute::class)->disableOriginalConstructor()->getMock();
        $attribute->expects($this->once())->method('getFrontendInput')->willReturn($frontendInput);
        $attribute->expects($this->once())->method('getFieldDepend')->willReturn($fieldDepend);
        $attribute->expects($this->once())->method('getShippingDepend')->willReturn($shippingDepend);
        if ($attributeData) {
            $attribute->method('getData')
                ->willReturnOnConsecutiveCalls($attributeData, $attributeData, $attributeData);
        }

        $attributes = [$attribute];
        $this->helperDataMock->expects($this->once())
            ->method('getFilteredAttributes')
            ->willReturn($attributes);

        $this->assertEquals(
            ['mpOaConfig' => $result],
            $this->defaultConfigProvider->getConfig()
        );
    }

    /**
     * @return array
     */
    public function providerGetConfig()
    {
        $data = [
            'isOscPage' => 1,
            'attributeDepend' => [],
            'shippingDepend' => [],
            'contentType' => [],
            'tinymceConfig' => '{"tinymce4":"example mock"}'
        ];

        $attributeDataMock = ['my_attribute' => 'test'];

        return [
            [
                $data,
                'text',
                false,
                false,
                []
            ],
            [
                array_merge($data, ['shippingDepend' => [$attributeDataMock]]),
                'text',
                false,
                true,
                $attributeDataMock
            ],
            [
                array_merge($data, ['attributeDepend' => [$attributeDataMock]]),
                'text',
                true,
                false,
                $attributeDataMock
            ],
            [
                array_merge(
                    $data,
                    [
                        'attributeDepend' => [$attributeDataMock],
                        'shippingDepend' => [$attributeDataMock]
                    ]
                ),
                'text',
                true,
                true,
                $attributeDataMock
            ],
            [
                array_merge($data, ['attributeDepend' => [$attributeDataMock]]),
                'select',
                false,
                false,
                $attributeDataMock
            ],
            [
                array_merge($data, ['attributeDepend' => [$attributeDataMock]]),
                'select',
                true,
                false,
                $attributeDataMock
            ],
            [
                array_merge(
                    $data,
                    [
                        'attributeDepend' => [$attributeDataMock],
                        'shippingDepend' => [$attributeDataMock]
                    ]
                ),
                'select',
                false,
                true,
                $attributeDataMock
            ],
            [
                array_merge($data, ['attributeDepend' => [$attributeDataMock]]),
                'select_visual',
                false,
                false,
                $attributeDataMock
            ],
            [
                array_merge($data, ['attributeDepend' => [$attributeDataMock]]),
                'select_visual',
                true,
                false,
                $attributeDataMock
            ],
            [
                array_merge(
                    $data,
                    [
                        'attributeDepend' => [$attributeDataMock],
                        'shippingDepend' => [$attributeDataMock]
                    ]
                ),
                'select_visual',
                false,
                true,
                $attributeDataMock
            ],
            [
                array_merge($data, ['attributeDepend' => [$attributeDataMock]]),
                'boolean',
                false,
                false,
                $attributeDataMock
            ],
            [
                array_merge($data, ['attributeDepend' => [$attributeDataMock]]),
                'boolean',
                true,
                false,
                $attributeDataMock
            ],
            [
                array_merge(
                    $data,
                    [
                        'attributeDepend' => [$attributeDataMock],
                        'shippingDepend' => [$attributeDataMock]
                    ]
                ),
                'boolean',
                false,
                true,
                $attributeDataMock
            ],
            [
                array_merge($data, ['contentType' => [$attributeDataMock]]),
                'textarea_visual',
                false,
                false,
                $attributeDataMock
            ],
            [
                array_merge(
                    $data,
                    [
                        'attributeDepend' => [$attributeDataMock],
                        'contentType' => [$attributeDataMock]
                    ]
                ),
                'textarea_visual',
                true,
                false,
                $attributeDataMock
            ],
            [
                array_merge(
                    $data,
                    [
                        'contentType' => [$attributeDataMock],
                        'shippingDepend' => [$attributeDataMock]
                    ]
                ),
                'textarea_visual',
                false,
                true,
                $attributeDataMock
            ],
            [
                array_merge(
                    $data,
                    [
                        'attributeDepend' => [$attributeDataMock],
                        'contentType' => [$attributeDataMock],
                        'shippingDepend' => [$attributeDataMock]
                    ]
                ),
                'textarea_visual',
                true,
                true,
                $attributeDataMock
            ],
        ];
    }
}
