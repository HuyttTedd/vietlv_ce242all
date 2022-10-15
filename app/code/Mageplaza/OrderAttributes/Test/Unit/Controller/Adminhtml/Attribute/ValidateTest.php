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
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\ViewInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\FormData;
use Magento\Framework\View\Element\Messages;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\OrderAttributes\Controller\Adminhtml\Attribute\Validate;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\AttributeFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ValidateTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Controller\Adminhtml\Attribute
 */
class ValidateTest extends TestCase
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
     * @var MockObject
     */
    private $viewMock;

    /**
     * @var MockObject
     */
    private $responseMock;

    /**
     * @var Validate
     */
    private $validateController;

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

        $this->viewMock = $this->createMock(ViewInterface::class);
        $this->responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->setMethods(['setBody'])
            ->getMockForAbstractClass();

        $contextMock->method('getResponse')->willReturn($this->responseMock);
        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $contextMock->method('getView')->willReturn($this->viewMock);
        $contextMock->method('getMessageManager')->willReturn($this->messageManagerMock);

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

        $this->validateController = new Validate(
            $contextMock,
            $this->pageFactoryMock,
            $this->registryMock,
            $this->orderAttributeMock,
            $this->helperDataMock,
            $this->formDataMock
        );
    }

    /**
     * @return array
     */
    public function providerTestExecuteWithNotFoundAttributeAndEmptyFrontendLabel()
    {
        return [
            [
                'The Attribute with the "1" ID doesn\'t exist.',
                0
            ],
            [
                'Default label is required.',
                1
            ],
        ];
    }

    /**
     * @param string $message
     * @param int $attributeId
     *
     * @throws LocalizedException
     * @dataProvider providerTestExecuteWithNotFoundAttributeAndEmptyFrontendLabel
     */
    public function testExecuteWithNotFoundAttributeAndEmptyFrontendLabel($message, $attributeId)
    {
        $attributeIdInput = 1;
        $attributeCode = 'my_attribute';
        $frontendLabel = '';
        $attributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderAttributeMock->expects($this->once())->method('create')->willReturn($attributeMock);
        $this->requestMock
            ->method('getParam')
            ->withConsecutive(['attribute_code'], ['attribute_id'], ['frontend_label'])
            ->willReturnOnConsecutiveCalls($attributeCode, $attributeIdInput, $frontendLabel);
        $attributeMock->expects($this->once())->method('load')->with($attributeIdInput)->willReturnSelf();
        $attributeMock->expects($this->once())->method('getId')->willReturn($attributeId);

        $this->processException($message);
    }

    /**
     * @return array
     */
    public function providerTestExecuteWithValidateAttributeCode()
    {
        $message = 'Attribute code "121~ax" is invalid. Please use only letters (a-z), numbers (0-9)' .
            'or underscore(_) in this field, first character should be a letter.';

        return [
            [
                '',
                'Attribute Code is required.',
                0,
                false
            ],
            [
                '121~ax',
                $message,
                0,
                false
            ],
            [
                'my_attribute',
                'An attribute with this code already exists.',
                1,
                false
            ],
            [
                'my_attribute',
                'An attribute with this code already exists in sales order.',
                0,
                true
            ]
        ];
    }

    /**
     * @param string $attributeCode
     * @param string $message
     * @param int $attributeId
     * @param boolean $isColumnExists
     *
     * @throws LocalizedException
     * @dataProvider providerTestExecuteWithValidateAttributeCode
     */
    public function testExecuteWithValidateAttributeCode($attributeCode, $message, $attributeId, $isColumnExists)
    {
        $attributeIdInput = 0;
        $attributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderAttributeMock->expects($this->once())->method('create')->willReturn($attributeMock);
        $this->requestMock
            ->method('getParam')
            ->withConsecutive(['attribute_code'], ['attribute_id'])
            ->willReturnOnConsecutiveCalls($attributeCode, $attributeIdInput);

        $attributeMock->method('loadByCode')->with($attributeCode)->willReturnSelf();
        $attributeMock->method('getId')->willReturn($attributeId);

        $attributeMock->method('isColumnExists')
            ->with($attributeCode)
            ->willReturn($isColumnExists);

        $this->processException($message);
    }

    /**
     * @throws LocalizedException
     */
    public function testExecuteWithValidateOption()
    {
        $attributeIdInput = 1;
        $attributeCode = 'my_attribute';
        $frontendLabel = 'Test';
        $frontendInput = 'select';

        $serializedOptions = '["option%5Border%5D%5Boption_0%5D=1&option%5Bvalue%5D%5Boption_0%5D%5B0%5D=sxx&option%5Bvalue%5D%5Boption_0%5D%5B1%5D=&option%5Bvalue%5D%5Boption_0%5D%5B2%5D=&option%5Bdelete%5D%5Boption_0%5D=","option%5Border%5D%5Boption_1%5D=2&option%5Bvalue%5D%5Boption_1%5D%5B0%5D=&option%5Bvalue%5D%5Boption_1%5D%5B1%5D=&option%5Bvalue%5D%5Boption_1%5D%5B2%5D=&option%5Bdelete%5D%5Boption_1%5D="]';
        $options = [
            'option' => [
                'order' => [
                    'option_0' => '1',
                    'option_1' => '2',
                ],
                'value' => [
                    'option_0' => [
                        0 => 'sxx',
                        1 => '',
                        2 => '',
                    ],
                    'option_1' => [
                        0 => '',
                        1 => '',
                        2 => '',
                    ],
                ],
                'delete' => [
                    'option_0' => '',
                    'option_1' => '',
                ],
            ]
        ];
        $attributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderAttributeMock->expects($this->once())->method('create')->willReturn($attributeMock);
        $this->requestMock
            ->method('getParam')
            ->withConsecutive(
                ['attribute_code'],
                ['attribute_id'],
                ['frontend_label'],
                ['frontend_input'],
                ['serialized_options', []]
            )
            ->willReturnOnConsecutiveCalls(
                $attributeCode,
                $attributeIdInput,
                $frontendLabel,
                $frontendInput,
                $serializedOptions
            );
        $attributeMock->expects($this->once())->method('load')->with($attributeIdInput)->willReturnSelf();
        $attributeMock->expects($this->once())->method('getId')->willReturn(1);

        $this->helperDataMock->expects($this->once())
            ->method('versionCompare')
            ->with('2.2.6')
            ->willReturn(true);
        $this->formDataMock->expects($this->once())->method('unserialize')
            ->with($serializedOptions)
            ->willReturn($options);

        $this->processException('The value of Admin scope can\'t be empty.');
    }

    /**
     * @return array
     */
    public function providerTestExecuteWithValidateOptionVisual()
    {
        return [
            ['select_visual'],
            ['multiselect_visual']
        ];
    }

    /**
     * @param string $frontendInput
     *
     * @throws LocalizedException
     * @dataProvider providerTestExecuteWithValidateOptionVisual
     */
    public function testExecuteWithValidateOptionVisual($frontendInput)
    {
        $attributeIdInput = 1;
        $attributeCode = 'my_attribute';
        $frontendLabel = 'Test';

        $options = [
            'order' => [
                'option_0' => '1',
                'option_1' => '2',
            ],
            'value' => [
                'option_0' => [
                    0 => 'sxx',
                    1 => '',
                    2 => '',
                ],
                'option_1' => [
                    0 => '',
                    1 => '',
                    2 => '',
                ],
            ],
            'delete' => [
                'option_0' => '',
                'option_1' => '',
            ],
        ];
        $attributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderAttributeMock->expects($this->once())->method('create')->willReturn($attributeMock);
        $this->requestMock
            ->method('getParam')
            ->withConsecutive(
                ['attribute_code'],
                ['attribute_id'],
                ['frontend_label'],
                ['frontend_input'],
                ['optionvisual']
            )
            ->willReturnOnConsecutiveCalls(
                $attributeCode,
                $attributeIdInput,
                $frontendLabel,
                $frontendInput,
                $options
            );
        $attributeMock->expects($this->once())->method('load')->with($attributeIdInput)->willReturnSelf();
        $attributeMock->expects($this->once())->method('getId')->willReturn(1);

        $this->processException('The value of Admin scope can\'t be empty.');
    }

    /**
     * @param string $message
     *
     * @throws LocalizedException
     */
    public function processException($message)
    {
        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(new Phrase($message))
            ->willReturnSelf();
        $layoutMock = $this->getMockBuilder(LayoutInterface::class)
            ->setMethods(['initMessages', 'getMessagesBlock'])
            ->getMockForAbstractClass();
        $this->viewMock->expects($this->exactly(2))
            ->method('getLayout')
            ->willReturn($layoutMock);
        $layoutMock->expects($this->once())->method('initMessages');
        $messageBockMock = $this->getMockBuilder(Messages::class)
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock->expects($this->once())->method('getMessagesBlock')->willReturn($messageBockMock);
        $messageBockMock->expects($this->once())->method('getGroupedHtml')->willReturn('test');

        $this->responseMock->expects($this->once())
            ->method('setBody')
            ->with('{"error":true,"html_message":"test"}')
            ->willReturnSelf();

        $this->validateController->execute();
    }
}
