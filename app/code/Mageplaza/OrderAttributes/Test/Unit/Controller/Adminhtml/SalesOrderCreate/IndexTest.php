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

namespace Mageplaza\OrderAttributes\Test\Unit\Controller\Adminhtml\SalesOrderCreate;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session\Quote;
use Magento\Framework\App\RequestInterface;
use Mageplaza\OrderAttributes\Controller\Adminhtml\SalesOrderCreate\Index;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Quote as QuoteAttribute;
use Mageplaza\OrderAttributes\Model\QuoteFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class IndexTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Controller\Adminhtml\SalesOrderCreate
 */
class IndexTest extends TestCase
{
    /**
     * @var string
     */
    protected $scope = 'mpOrderAttributes';

    /**
     * @var QuoteFactory
     */
    protected $quoteAttributeFactoryMock;

    /**
     * @var Data
     */
    protected $helperDataMock;

    /**
     * @var Quote
     */
    protected $quoteMock;

    /**
     * @var RequestInterface|MockObject
     */
    protected $requestMock;

    /**
     * @var Index
     */
    protected $indexController;

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            $callOriginalClone = true,
            $callAutoload = true,
            $mockedMethods = ['getPost']
        );
        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $this->quoteAttributeFactoryMock = $this->getMockBuilder(QuoteFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->helperDataMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteMock = $this->getMockBuilder(Quote::class)
            ->setMethods(['getStoreId', 'getQuoteId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->indexController = new Index(
            $contextMock,
            $this->quoteAttributeFactoryMock,
            $this->helperDataMock,
            $this->quoteMock
        );
    }

    public function testExecuteWithDisableModule()
    {
        $storeId = 1;
        $data = ['my_attribute' => 'test'];
        $this->requestMock->expects($this->once())
            ->method('getPost')
            ->with('mpOrderAttributes')
            ->willReturn($data);

        $this->quoteMock->expects($this->once())->method('getStoreId')->willReturn($storeId);

        $this->helperDataMock->expects($this->once())->method('isEnabled')->with($storeId)->willReturn(false);

        $this->indexController->execute();
    }

    public function testExecute()
    {
        $storeId = 1;
        $quoteId = 1;

        $data = ['my_attribute' => 'test'];
        $this->requestMock->expects($this->once())
            ->method('getPost')
            ->with('mpOrderAttributes')
            ->willReturn($data);

        $this->quoteMock->expects($this->once())->method('getStoreId')->willReturn($storeId);

        $this->helperDataMock->expects($this->once())->method('isEnabled')->with($storeId)->willReturn(true);
        $quoteAttributeMock = $this->getMockBuilder(QuoteAttribute::class)->disableOriginalConstructor()->getMock();
        $this->quoteAttributeFactoryMock->expects($this->once())->method('create')->willReturn($quoteAttributeMock);
        $this->quoteMock->expects($this->once())->method('getQuoteId')->willReturn($quoteId);
        $quoteAttributeMock->expects($this->once())
            ->method('saveAttributeData')
            ->with($quoteId, $data)->willReturnSelf();

        $this->indexController->execute();
    }
}
