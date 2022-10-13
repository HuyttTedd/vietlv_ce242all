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

namespace Mageplaza\OrderAttributes\Test\Unit\Block\Adminhtml\Attribute\Edit\Tab\Renderer;

use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit\Tab\Renderer\ShippingMethod;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * /**
 * Class ShippingMethodTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Block\Adminhtml\Attribute\Edit\Tab\Renderer
 */
class ShippingMethodTest extends TestCase
{
    /**
     * @var Registry|MockObject
     */
    private $_coreRegistryMock;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var ShippingMethod
     */
    private $shippingMethodBlock;

    protected function setUp()
    {
        $this->_coreRegistryMock = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()->getMock();
        $this->helperDataMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();
        $objectManagerHelper = new ObjectManager($this);
        $this->shippingMethodBlock = $objectManagerHelper->getObject(
            ShippingMethod::class,
            [
                'coreRegistry' => $this->_coreRegistryMock,
                'helperData' => $this->helperDataMock
            ]
        );
    }

    public function testGetElementHtml()
    {
        $carriers = [
            [
                'label' => 'Best Way',
                'value' => [
                    [
                        'value' => 'tablerate_bestway',
                        'label' => 'Table Rate'
                    ]
                ]
            ],
            [
                'label' => 'Free Shipping',
                'value' => [
                    [
                        'value' => 'freeshipping_freeshipping',
                        'label' => 'Free'
                    ]
                ]
            ]
        ];
        $this->helperDataMock->expects($this->once())
            ->method('getShippingMethods')
            ->willReturn($carriers);
        $orderAttributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->_coreRegistryMock->expects($this->once())
            ->method('registry')
            ->with('entity_attribute')
            ->willReturn($orderAttributeMock);
        $orderAttributeMock->expects($this->once())->method('getShippingDepend')->willReturn('tablerate_bestway');

        $result = '<select name="shipping_depend[]" id="shipping_depend" size="10" multiple="multiple"  ';
        $result .= 'class=" select multiselect admin__control-multiselect">';
        $result .= '<optgroup label="Best Way">';
        $result .= '<option value="tablerate_bestway" selected>Table Rate</option>';
        $result .= '</optgroup>';
        $result .= '<optgroup label="Free Shipping">';
        $result .= '<option value="freeshipping_freeshipping">Free</option>';
        $result .= '</optgroup>';
        $result .= '</select>';
        $result .= '<div id="mp-select-all-container">';
        $result .= '<input id="mp-select-all" type="checkbox" value="select_all_methods" />';
        $result .= '<label for="mp-select-all">Select All</label>';
        $result .= '</div>';

        $this->assertEquals($result, $this->shippingMethodBlock->getElementHtml());
    }
}
