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

namespace Mageplaza\OrderAttributes\Test\Unit\Block\Order\View;

use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\OrderAttributes\Block\Order\View\Additional;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\Collection;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionClass;
use ReflectionException;

/**
 * Class TestAdditional
 * @package Mageplaza\OrderAttributes\Test\Unit\Block\Order\View
 */
class TestAdditional extends TestCase
{
    /**
     * @var Additional
     */
    private $additional;

    /**
     * @var Json|PHPUnit_Framework_MockObject_MockObject
     */
    private $json;

    /**
     * @var Attribute|PHPUnit_Framework_MockObject_MockObject
     */
    private $attribute;

    /**
     * @var Escaper|PHPUnit_Framework_MockObject_MockObject
     */
    private $escaper;

    /**
     * @var Context|MockObject
     */
    private $context;

    /**
     * @var Data|MockObject
     */
    private $dataHelper;

    /**
     * @var EncoderInterface|MockObject
     */
    private $urlEncoder;

    /**
     * @var UrlInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $_urlBuilder;

    /**
     * @var CollectionFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionFactory;

    /** @var  Collection|PHPUnit_Framework_MockObject_MockObject */
    private $collectionMock;

    /**
     * @var Registry|MockObject
     */
    private $registryMock;

    /**
     * @throws ReflectionException
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->json = $this->getMockBuilder(Json::class)->disableOriginalConstructor()->getMock();
        $this->attribute = $this->getMockBuilder(Attribute::class)->disableOriginalConstructor()->getMock();
        $this->dataHelper = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $this->registryMock = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
        $this->urlEncoder = $this->getMockForAbstractClass(EncoderInterface::class, [], '', false);
        $this->_urlBuilder = $this->getMockForAbstractClass(UrlInterface::class, [], '', false);
        $this->collectionMock = $this->getMockBuilder(Collection::class)->disableOriginalConstructor()->getMock();
        $this->collectionFactory = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->collectionFactory->method('create')->willReturn($this->collectionMock);
        $this->context = $this->createMock(Context::class);
        $this->escaper = $this->getMockBuilder(Escaper::class)
            ->disableOriginalConstructor()
            ->setMethods(['escapeHtml'])
            ->getMock();
        $this->context->method('getEscaper')->willReturn($this->escaper);
        $this->context->method('getUrlBuilder')->willReturn($this->_urlBuilder);

        $this->additional = $objectManager->getObject(
            Additional::class,
            [
                'context' => $this->context,
                'registry' => $this->registryMock,
                'json' => $this->json,
                'dataHelper' => $this->dataHelper,
                'urlEncoder' => $this->urlEncoder,
                'collectionFactory' => $this->collectionFactory
            ]
        );
    }

    public function testGetAttributesWithEmptyOrder()
    {
        $this->assertEmpty($this->additional->getAttributes(1));
    }

    /**
     * @param string $result
     * @param DataObject $orderData
     * @param array $attributes
     * @param bool $isVisible
     * @param string $area
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     *
     * @dataProvider providerGetAttributes
     */
    public function testGetAttributes($result, $orderData, $attributes, $isVisible, $area)
    {
        $position = 1;
        $this->registryMock->expects($this->once())->method('registry')->with('current_order')
            ->willReturn($orderData);
        $this->collectionMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with('position', ['in' => $position])
            ->willReturnSelf();
        $this->collectionMock->expects($this->once())->method('getItems')->willReturn($attributes);
        $this->dataHelper->expects($this->atLeastOnce())->method('isVisible')->willReturn($isVisible);
        $this->assertEquals($result, $this->additional->getAttributes($position, $area));
    }

    /**
     * @return array
     */
    public function providerGetAttributes()
    {
        return [
            [
                '<strong>Attribute Label: </strong><span class=\'text-attribute\'></span><br>',
                new DataObject(['store_id' => 1, 'my_attribute' => 'test',]),
                $this->getItemsMock(),
                true,
                'admin',
            ],
            [
                '',
                new DataObject(['store_id' => 1, 'my_attribute' => null,]),
                $this->getItemsMock(),
                true,
                'admin',
            ],
            [
                '',
                new DataObject(['store_id' => 1, 'my_attribute' => null,]),
                $this->getItemsMock(),
                false,
                'admin',
            ],
            [
                '',
                new DataObject(['store_id' => 1, 'my_attribute' => 'test',]),
                $this->getItemsMock(false),
                false,
                'customer',
            ],
            [
                '<strong>Attribute Label: </strong><span class=\'text-attribute\'></span><br>',
                new DataObject(['store_id' => 1, 'my_attribute' => 'test',]),
                $this->getItemsMock(),
                true,
                'customer',
            ]
        ];
    }

    /**
     * @param bool $showInFrontendOrder
     *
     * @return array
     */
    public function getItemsMock($showInFrontendOrder = true)
    {
        $items = [];
        $items[] = new DataObject(
            [
                'show_in_frontend_order' => $showInFrontendOrder,
                'frontend_input' => 'text',
                'attribute_code' => 'my_attribute',
                'frontend_label' => 'Attribute Label'
            ]
        );

        return $items;
    }

    /**
     * @param string $result
     * @param string $frontendInput
     * @param string $value
     *
     * @throws ReflectionException
     *
     * @dataProvider providerSelectAndMultiselect
     */
    public function testGetValueWithTypeSelectAndMultiselect($result, $frontendInput, $value)
    {
        $options = '{}';
        $storeId = 1;
        $this->attribute->expects($this->once())
            ->method('getOptions')->willReturn($options);
        $this->dataHelper->expects($this->once())
            ->method('prepareOptionValue')
            ->with($options, $value, $storeId)
            ->willReturn($result);

        $this->invokeGetValue($result, $frontendInput, $value);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetValueWithTypeDate()
    {
        $result = '04/22/2020';
        $value = $result;
        $frontendInput = 'date';
        $this->dataHelper->expects($this->once())
            ->method('prepareDateValue')
            ->with($value)
            ->willReturn($result);

        $this->invokeGetValue($result, $frontendInput, $value);
    }

    /**
     * @param string $result
     * @param string $frontendInput
     * @param string $value
     * @param string $urlEncodeResult
     *
     * @throws ReflectionException
     *
     * @dataProvider providerFileAndImage
     */
    public function testGetValueWithTypeFileAndImage($result, $frontendInput, $value, $urlEncodeResult)
    {
        $param = '/' . $frontendInput . '/' . $urlEncodeResult;
        $path = 'https://mydomain.com/mporderattributes/viewfile/index' . $param;

        $this->urlEncoder->expects($this->once())->method('encode')->with($value)->willReturn($urlEncodeResult);
        $this->_urlBuilder->expects($this->once())->method('getUrl')
            ->with('mporderattributes/viewfile/index' . $param)->willReturn($path);

        $this->invokeGetValue($result, $frontendInput, $value);
    }

    /**
     * @return array
     */
    public function providerFileAndImage()
    {
        $url = 'https://mydomain.com/mporderattributes/viewfile/index';
        $fileResult = '<a target="_blank" href="' . $url . '/file/L2cvZC90ZXN0LnppcA,,">test.zip</a>';

        $path = $url . '/image/L2QvYi9kYjJhNDNiNDBmNTVhMzAxN2VhNmRkZmRkNmI1N2VmZl8xLnBuZw,,';
        $imageResult = '<br/><a target="_blank" href="' . $path . '">';
        $imageResult .= '<img style="max-height: 100px" ';
        $imageResult .= 'title="' . __('View Full Size') . '" ';
        $imageResult .= 'src="' . $path . '" ';
        $imageResult .= 'alt="/d/b/db2a43b40f55a3017ea6ddfdd6b57eff_1.png">';
        $imageResult .= '</a>';

        return [
            [
                $fileResult,
                'file',
                '/g/d/test.zip',
                'L2cvZC90ZXN0LnppcA,,'
            ],
            [
                $imageResult,
                'image',
                '/d/b/db2a43b40f55a3017ea6ddfdd6b57eff_1.png',
                'L2QvYi9kYjJhNDNiNDBmNTVhMzAxN2VhNmRkZmRkNmI1N2VmZl8xLnBuZw,,'
            ]
        ];
    }

    /**
     * @return array
     */
    public function providerSelectAndMultiselect()
    {
        return [
            ['', 'select', ''],
            ['', 'multiselect', ''],
            ['', 'select_visual', ''],
            ['', 'multiselect_visual', ''],
        ];
    }

    /**
     * @param string $result
     * @param string $frontendInput
     * @param string $value
     *
     * @throws ReflectionException
     *
     * @dataProvider providerBoolean
     */
    public function testGetValueWithTypeBoolean($result, $frontendInput, $value)
    {
        $this->dataHelper->expects($this->once())
            ->method('prepareBoolValue')
            ->with($value)
            ->willReturn($result);

        $this->invokeGetValue($result, $frontendInput, $value);
    }

    /**
     * @return array
     */
    public function providerBoolean()
    {
        return [
            [__('Yes'), 'boolean', __('Yes')],
            [__('No'), 'boolean', __('No')],
        ];
    }

    /**
     * @param string $result
     * @param string $frontendInput
     * @param string $value
     *
     * @throws ReflectionException
     *
     * @dataProvider providerTextAndTextarea
     */
    public function testGetValueWithTypeTextAndTextarea($result, $frontendInput, $value)
    {
        $this->escaper->expects($this->atLeastOnce())
            ->method('escapeHtml')
            ->with($value)
            ->willReturn($result);

        $this->invokeGetValue($result, $frontendInput, $value);
    }

    /**
     * @return array
     */
    public function providerTextAndTextarea()
    {
        return [
            ['Text value', 'text', 'Text value'],
            ['Textarea value', 'textarea', 'Textarea value'],
        ];
    }

    /**
     * @param string $result
     * @param string $frontendInput
     * @param string $value
     *
     * @throws ReflectionException
     */
    public function invokeGetValue($result, $frontendInput, $value)
    {
        $this->attribute->expects($this->once())
            ->method('getFrontendInput')
            ->willReturn($frontendInput);

        $storeId = 1;
        $additional = new ReflectionClass(Additional::class);
        $method = $additional->getMethod('getValue');
        $method->setAccessible(true);

        $this->assertEquals($result, $method->invokeArgs($this->additional, [$this->attribute, $storeId, $value]));
    }

    /**
     * @return array
     */
    public function providerGetValue()
    {
        return [
            ['Image', 'text', 1, 'Image'],
            [__('Yes'), 'boolean', 1, 1],
            [__('No'), 'boolean', 1, 0]
        ];
    }

    /**
     * @param int $storeId
     * @param string $labels
     * @param array $labelsDecode
     * @param string $frontendLabel
     * @param string $result
     *
     * @throws ReflectionException
     *
     * @dataProvider providerTestGetLabel
     */
    public function testGetLabel($storeId, $labels, $frontendLabel, $labelsDecode, $result)
    {
        $this->json->expects($this->once())->method('unserialize')
            ->with($labels)
            ->willReturn($labelsDecode);

        $this->attribute->expects($this->once())
            ->method('getLabels')
            ->willReturn($labels);

        if ($frontendLabel) {
            $this->attribute->expects($this->once())->method('getFrontendLabel')->willReturn($frontendLabel);
        }

        $additional = new ReflectionClass(Additional::class);
        $method = $additional->getMethod('getLabel');
        $method->setAccessible(true);
        $this->assertEquals($result, $method->invokeArgs($this->additional, [$this->attribute, $storeId]));
    }

    /**
     * @return array
     */
    public function providerTestGetLabel()
    {
        $labels = '{\"1\":\"Image\",\"2\":\"\"}';
        $labelsDecode = [1 => 'Image', 2 => '',];

        return [
            [1, $labels, '', $labelsDecode, 'Image'],
            [0, $labels, 'Test', $labelsDecode, 'Test']
        ];
    }
}
