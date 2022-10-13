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

namespace Mageplaza\OrderAttributes\Test\Unit\Ui\Component;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Ui\Component\ColumnFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class TestColumnFactory
 * @package Mageplaza\OrderAttributes\Test\Unit\Ui\Component
 */
class TestColumnFactory extends TestCase
{
    /**
     * @var ColumnFactory
     */
    private $columnFactory;

    /**
     * @var UiComponentFactory|MockObject
     */
    private $componentFactoryMock;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->componentFactoryMock = $this->getMockBuilder(UiComponentFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->helperDataMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();

        $this->columnFactory = new ColumnFactory(
            $this->componentFactoryMock,
            $this->helperDataMock
        );
    }

    /**
     * @return array
     */
    public function providerCreate()
    {
        $textConfig = [
            'dataType' => 'text',
            'component' => 'Magento_Ui/js/grid/columns/column',
            'filter' => 'text'
        ];
        $config = [
            'dataType' => 'select',
            'component' => 'Magento_Ui/js/grid/columns/select',
            'filter' => 'select'
        ];

        $booleanConfig = array_merge($config, [
            'options' => [
                ['value' => '0', 'label' => __('No')],
                ['value' => '1', 'label' => __('Yes')]
            ]
        ]);

        $selectConfig = array_merge($config, [
            'options' => [
                ['value' => 'option_0', 'label' => __('Option 1')],
                ['value' => 'option_1', 'label' => __('Option 2')]
            ]
        ]);

        $options = [
            'option' => [
                'value' => [
                    'option_0' => ['Option 1'],
                    'option_1' => ['Option 2']
                ]
            ]
        ];
        $optionsVisual = [
            'optionvisual' => [
                'value' => [
                    'option_0' => ['Option 1'],
                    'option_1' => ['Option 2']
                ]
            ]
        ];

        $jsonOptions = '{"option":{"value":{"option_0":["Option 1"],"option_1":["Option 2"]}}}';
        $jsonOptionsVisual = '{"optionvisual":{"value":{"option_0":["Option 1"],"option_1":["Option 2"]}}}';

        $dateConfig = [
            'dataType' => 'date',
            'component' => 'Magento_Ui/js/grid/columns/date',
            'filter' => 'dateRange',
            'dateFormat' => 'MMM d, y'
        ];

        return [
            ['text', $textConfig],
            ['boolean', $booleanConfig],
            ['date', $dateConfig],
            ['select', $selectConfig, $jsonOptions, $options],
            ['multiselect', $selectConfig, $jsonOptions, $options],
            ['select_visual', $selectConfig, $jsonOptionsVisual, $optionsVisual],
            ['multiselect_visual', $selectConfig, $jsonOptionsVisual, $optionsVisual]
        ];
    }

    /**
     * @param string $frontendInput
     * @param array $config
     * @param string $json
     * @param array $options
     *
     * @throws LocalizedException
     * @dataProvider providerCreate
     */
    public function testCreate($frontendInput, $config, $json = '', $options = [])
    {
        $attributeCode = 'test_attribute';
        $frontendLabel = __('Label');
        $config['label'] = $frontendLabel;

        /**
         * @var ContextInterface $context
         */
        $context = $this->getMockBuilder(ContextInterface::class)->getMockForAbstractClass();

        /**
         * @var Attribute|MockObject $attribute
         */
        $attribute = $this->getMockBuilder(Attribute::class)->disableOriginalConstructor()->getMock();
        $attribute->expects($this->once())->method('getFrontendInput')->willReturn($frontendInput);
        $attribute->expects($this->once())->method('getAttributeCode')->willReturn($attributeCode);
        $attribute->expects($this->once())->method('getFrontendLabel')->willReturn($frontendLabel);
        if (in_array($frontendInput, ['select', 'multiselect', 'select_visual', 'multiselect_visual'], true)) {
            $attribute->expects($this->once())->method('getOptions')->willReturn($json);
            $this->helperDataMock->expects($this->once())->method('jsonDecodeData')->with($json)->willReturn($options);
        }

        $arguments = [
            'data' => ['config' => $config],
            'context' => $context,
        ];

        $column = $this->objectManager->getObject(
            Column::class,
            [
                'context' => $context,
                'data' => [
                    'config' => $config,
                ]
            ]
        );

        $this->componentFactoryMock->expects($this->once())->method('create')
            ->with($attributeCode, 'column', $arguments)
            ->willReturn($column);

        $this->assertEquals($column, $this->columnFactory->create($attribute, $context));
    }
}
