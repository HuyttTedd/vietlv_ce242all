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

namespace Mageplaza\OrderAttributes\Test\Unit\Controller\Adminhtml\Attribute;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\FormData;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\OrderAttributes\Controller\Adminhtml\Attribute\Delete;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\AttributeFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class DeleteTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Controller\Adminhtml\Attribute
 */
class DeleteTest extends TestCase
{
    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var PageFactory|MockObject
     */
    private $pageFactoryMock;

    /**
     * @var Registry|MockObject
     */
    private $registryMock;

    /**
     * @var AttributeFactory|MockObject
     */
    private $orderAttributeMock;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var FormData|MockObject
     */
    private $formDataMock;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManagerMock;

    /**
     * @var ResultFactory|MockObject
     */
    private $resultFactoryMock;

    /**
     * @var EventManagerInterface|MockObject
     */
    private $eventManagerMock;

    /**
     * @var Delete
     */
    private $deleteController;

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);
        $this->eventManagerMock = $this->getMockForAbstractClass(EventManagerInterface::class);
        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $contextMock->method('getMessageManager')->willReturn($this->messageManagerMock);
        $contextMock->method('getResultFactory')->willReturn($this->resultFactoryMock);
        $contextMock->method('getEventManager')->willReturn($this->eventManagerMock);

        $this->pageFactoryMock = $this->getMockBuilder(PageFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->registryMock = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderAttributeMock = $this->getMockBuilder(AttributeFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->helperDataMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->formDataMock = $this->getMockBuilder(FormData::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->deleteController = new Delete(
            $contextMock,
            $this->pageFactoryMock,
            $this->registryMock,
            $this->orderAttributeMock,
            $this->helperDataMock,
            $this->formDataMock
        );
    }

    public function testExecuteWithNotFoundAttributeId()
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn(false);
        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(new Phrase('We can\'t find an attribute to delete.'))
            ->willReturnSelf();

        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($resultRedirectMock);

        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('mporderattributes/*/', [])
            ->willReturnSelf();

        $this->deleteController->execute();
    }

    public function testExecuteWithException()
    {
        $attributeId = 1;
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($attributeId);

        $attributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderAttributeMock->expects($this->once())->method('create')->willReturn($attributeMock);
        $attributeMock->expects($this->once())->method('load')->with($attributeId)->willReturnSelf();
        $exception = new Exception('');
        $attributeMock->expects($this->once())->method('delete')->willThrowException($exception);
        $this->messageManagerMock->expects($this->once())
            ->method('addExceptionMessage')
            ->with($exception, new Phrase('We can\'t delete the attribute right now.'))
            ->willReturnSelf();
        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($resultRedirectMock);

        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('mporderattributes/*/edit', ['id' => $attributeId, '_current' => true])
            ->willReturnSelf();

        $this->deleteController->execute();
    }

    public function testExecute()
    {
        $attributeId = 1;
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($attributeId);

        $attributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderAttributeMock->expects($this->once())->method('create')->willReturn($attributeMock);
        $attributeMock->expects($this->once())->method('load')->with($attributeId)->willReturnSelf();

        $attributeMock->expects($this->once())->method('delete')->willReturnSelf();
        $this->eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with('mporderattributes_attribute_delete', ['attribute' => $attributeMock]);
        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(new Phrase('The attribute has been deleted.'))
            ->willReturnSelf();

        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($resultRedirectMock);

        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('mporderattributes/*/', [])
            ->willReturnSelf();

        $this->deleteController->execute();
    }
}
