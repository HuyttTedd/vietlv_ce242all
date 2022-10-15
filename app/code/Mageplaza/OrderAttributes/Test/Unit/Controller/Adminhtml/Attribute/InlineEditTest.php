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
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Phrase;
use Mageplaza\OrderAttributes\Controller\Adminhtml\Attribute\InlineEdit;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\AttributeFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Class InlineEditTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Controller\Adminhtml\Attribute
 */
class InlineEditTest extends TestCase
{
    /**
     * @var JsonFactory|MockObject
     */
    private $jsonFactoryMock;

    /**
     * @var AttributeFactory|MockObject
     */
    private $attributeFactoryMock;

    /**
     * @var InlineEdit
     */
    private $inlineEditController;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $contextMock->method('getRequest')->willReturn($this->requestMock);

        $this->jsonFactoryMock = $this->getMockBuilder(JsonFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->attributeFactoryMock = $this->getMockBuilder(AttributeFactory::class)
            ->disableOriginalConstructor()->getMock();

        $this->inlineEditController = new InlineEdit(
            $contextMock,
            $this->jsonFactoryMock,
            $this->attributeFactoryMock
        );
    }

    /**
     * @return array
     */
    public function providerTestExecuteWithEmptyData()
    {
        return [
            [
                'Please correct the data sent.',
                [],
                false
            ],
            [
                'Default label value must not be empty.',
                [
                    [
                        'frontend_label' => ''
                    ]
                ],
                false
            ]
        ];
    }

    /**
     * @param string $message
     * @param array $items
     * @param boolean $isAjax
     *
     * @dataProvider providerTestExecuteWithEmptyData
     */
    public function testExecuteWithEmptyData($message, $items, $isAjax)
    {
        $jsonMock = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()->getMock();
        $this->jsonFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($jsonMock);
        $this->requestMock->expects($this->at(0))->method('getParam')->with('items')->willReturn($items);
        if (!$items) {
            $this->requestMock->expects($this->at(1))->method('getParam')->with('isAjax')->willReturn($isAjax);
        }

        $jsonMock->expects($this->once())
            ->method('setData')
            ->with([
                'messages' => [new Phrase($message)],
                'error' => true,
            ])->willReturnSelf();

        $this->assertEquals($jsonMock, $this->inlineEditController->execute());
    }

    /**
     * @return array
     */
    public function providerTestExecute()
    {
        return [
            [
                [
                    'messages' => [new Phrase('[ID: 1] runtime message')],
                    'error' => true,
                ],
                new RuntimeException('runtime message')
            ],
            [
                [
                    'messages' => [new Phrase('[ID: 1] Something went wrong while saving the entity.')],
                    'error' => true,
                ],
                new Exception('test')
            ],
            [
                ['messages' => [], 'error' => false],
                false
            ]
        ];
    }

    /**
     * @param array $messageResult
     * @param RuntimeException|Exception $exception
     *
     * @dataProvider providerTestExecute
     */
    public function testExecute($messageResult, $exception)
    {
        $objectId = 1;
        $objectData = ['frontend_label' => 'test'];

        $jsonMock = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()->getMock();
        $this->jsonFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($jsonMock);
        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('items')
            ->willReturn([$objectId => $objectData]);
        $attributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->attributeFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($attributeMock);
        $attributeMock->expects($this->once())->method('load')->with($objectId)->willReturnSelf();

        $attributeMock->expects($this->once())
            ->method('addData')
            ->with($objectData)->willReturnSelf();
        if ($exception) {
            $attributeMock->expects($this->once())->method('getId')->willReturn($objectId);
            $attributeMock->expects($this->once())->method('save')->willThrowException($exception);
        } else {
            $attributeMock->expects($this->once())->method('save')->willReturnSelf();
        }

        $jsonMock->expects($this->once())->method('setData')->with($messageResult)->willReturnSelf();

        $this->assertEquals($jsonMock, $this->inlineEditController->execute());
    }
}
