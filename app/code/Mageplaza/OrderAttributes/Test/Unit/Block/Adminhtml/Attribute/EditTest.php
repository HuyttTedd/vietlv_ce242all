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

namespace Mageplaza\OrderAttributes\Test\Unit\Block\Adminhtml\Attribute;

use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit;
use Mageplaza\OrderAttributes\Model\Attribute;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class EditTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Block\Adminhtml\Attribute
 */
class EditTest extends TestCase
{
    /**
     * @var Registry|MockObject
     */
    private $_coreRegistryMock;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlBuilderMock;

    /**
     * @var Edit
     */
    private $editBlock;

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->urlBuilderMock = $this->getMockBuilder(UrlInterface::class)->getMock();
        $buttonListMock = $this->getMockBuilder(ButtonList::class)->disableOriginalConstructor()->getMock();
        $requestMock = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()->getMock();

        $contextMock->method('getUrlBuilder')->willReturn($this->urlBuilderMock);
        $contextMock->method('getButtonList')->willReturn($buttonListMock);
        $contextMock->method('getRequest')->willReturn($requestMock);

        $this->_coreRegistryMock = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->editBlock = new Edit(
            $contextMock,
            $this->_coreRegistryMock
        );
    }

    public function testGetHeaderTextWithEditOrderAttribute()
    {
        $attributeId = 1;
        $frontendLabel = 'Test';
        $result = new Phrase('Edit Order Attribute "%1"', ['Test']);
        $orderAttributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()->getMock();
        $this->_coreRegistryMock->expects($this->once())
            ->method('registry')
            ->with('entity_attribute')
            ->willReturn($orderAttributeMock);
        $orderAttributeMock->expects($this->once())->method('getId')->willReturn($attributeId);
        $orderAttributeMock->expects($this->once())->method('getFrontendLabel')->willReturn($frontendLabel);

        $this->assertEquals($result, $this->editBlock->getHeaderText());
    }

    public function testGetHeaderTextWithNewOrderAttribute()
    {
        $orderAttributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()->getMock();
        $this->_coreRegistryMock->expects($this->once())
            ->method('registry')
            ->with('entity_attribute')
            ->willReturn($orderAttributeMock);

        $this->assertEquals(new Phrase('New Order Attribute'), $this->editBlock->getHeaderText());
    }

    public function testGetValidationUrl()
    {
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('mporderattributes/*/validate', ['_current' => true])->willReturn('some_url');

        $this->assertEquals('some_url', $this->editBlock->getValidationUrl());
    }

    public function testGetSaveUrl()
    {
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('mporderattributes/*/save', ['_current' => true, 'back' => null])->willReturn('save_url');

        $this->assertEquals('save_url', $this->editBlock->getSaveUrl());
    }
}
