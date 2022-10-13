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

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\FormData;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\OrderAttributes\Controller\Adminhtml\Attribute\Edit;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\AttributeFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class EditTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Controller\Adminhtml\Attribute
 */
class EditTest extends TestCase
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
     * @var Session|MockObject
     */
    private $backendSessionMock;

    /**
     * @var Edit
     */
    private $editController;

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
        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->backendSessionMock = $this->getMockBuilder(Session::class)
            ->setMethods(['getAttributeData'])
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $contextMock->method('getMessageManager')->willReturn($this->messageManagerMock);
        $contextMock->method('getResultFactory')->willReturn($this->resultFactoryMock);
        $contextMock->method('getSession')->willReturn($this->backendSessionMock);

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

        $this->editController = new Edit(
            $contextMock,
            $this->pageFactoryMock,
            $this->registryMock,
            $this->orderAttributeMock,
            $this->helperDataMock,
            $this->formDataMock
        );
    }

    public function testExecuteWithNotFoundAttribute()
    {
        $attributeId = 1;
        $attributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderAttributeMock->expects($this->once())->method('create')->willReturn($attributeMock);
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($attributeId);
        $attributeMock->expects($this->once())->method('load')->with($attributeId)->willReturnSelf();
        $attributeMock->expects($this->once())->method('getId')->willReturn(0);
        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(new Phrase('This attribute no longer exists.'))
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

        $this->editController->execute();
    }

    /**
     * @return array
     */
    public function providerTestExecute()
    {
        return [
            [
                0,
                'New Order Attribute'
            ],
            [
                1,
                'Attribute Image'
            ]
        ];
    }

    /**
     * @param int $attributeId
     * @param string $pageTitle
     *
     * @dataProvider providerTestExecute
     */
    public function testExecute($attributeId, $pageTitle)
    {
        $data = ['my_attribute' => 1];
        $attributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderAttributeMock->expects($this->once())->method('create')->willReturn($attributeMock);
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($attributeId);

        if ($attributeId) {
            $attributeMock->expects($this->once())->method('load')->with($attributeId)->willReturnSelf();
            $attributeMock->expects($this->once())->method('getId')->willReturn($attributeId);
            $attributeMock->expects($this->once())->method('getFrontendLabel')->willReturn($pageTitle);
        }

        $this->backendSessionMock->expects($this->once())
            ->method('getAttributeData')
            ->with(true)
            ->willReturn($data);
        $attributeMock->expects($this->once())->method('addData')->with($data)->willReturnSelf();
        $this->registryMock->expects($this->once())->method('register')->with('entity_attribute', $attributeMock);
        $resultPageMock = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resultPageMock->expects($this->once())
            ->method('setActiveMenu')
            ->with('Mageplaza_OrderAttributes::attribute')
            ->willReturnSelf();
        $this->pageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultPageMock);
        $configMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $titleMock = $this->getMockBuilder(Title::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resultPageMock->expects($this->once())->method('getConfig')->willReturn($configMock);
        $configMock->expects($this->once())->method('getTitle')->willReturn($titleMock);
        $titleMock->expects($this->once())->method('prepend')->with(new Phrase($pageTitle));

        $this->editController->execute();
    }
}
