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

namespace Mageplaza\OrderAttributes\Test\Unit\Observer;

use Magento\Framework\App\Area;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\AttributesRepository;
use Mageplaza\OrderAttributes\Model\FileResult;
use Mageplaza\OrderAttributes\Model\Quote as QuoteAttribute;
use Mageplaza\OrderAttributes\Model\QuoteFactory as QuoteAttributeFactory;
use Mageplaza\OrderAttributes\Observer\QuoteSubmitBefore;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Zend_Validate_Exception;
use Zend_Validate_File_Upload;

/**
 * Class TestQuoteSubmitBefore
 * @package Mageplaza\OrderAttributes\Test\Unit\Observer
 */
class TestQuoteSubmitBefore extends TestCase
{
    /**
     * @var QuoteAttributeFactory|MockObject
     */
    private $quoteAttributeFactoryMock;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var Zend_Validate_File_Upload|MockObject
     */
    private $fileUploadMock;

    /**
     * @var AttributesRepository|MockObject
     */
    private $attributeRepositoryMock;

    /**
     * @var Observer|MockObject
     */
    private $observerMock;

    /**
     * @var Quote|MockObject
     */
    private $quoteMock;

    /**
     * @var Order|MockObject
     */
    private $orderMock;

    /**
     * @var QuoteAttribute|MockObject
     */
    private $quoteAttributeMock;

    /**
     * @var QuoteSubmitBefore
     */
    private $quoteSubmitBefore;

    /**
     * @throws ReflectionException
     */
    protected function setup()
    {
        $this->quoteAttributeFactoryMock = $this->getMockBuilder(QuoteAttributeFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->helperDataMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();
        $this->requestMock = $this->getMockBuilder(RequestInterface::class)
            ->setMethods(['getPostValue'])->getMockForAbstractClass();
        $this->fileUploadMock = $this->getMockBuilder(Zend_Validate_File_Upload::class)
            ->disableOriginalConstructor()->getMock();
        $this->attributeRepositoryMock = $this->getMockBuilder(AttributesRepository::class)
            ->disableOriginalConstructor()->getMock();
        $this->prepareObserver();

        $this->quoteSubmitBefore = new QuoteSubmitBefore(
            $this->quoteAttributeFactoryMock,
            $this->helperDataMock,
            $this->requestMock,
            $this->fileUploadMock,
            $this->attributeRepositoryMock
        );
    }

    /**
     * @throws ReflectionException
     */
    public function prepareObserver()
    {
        $this->observerMock = $this->createMock(Observer::class);
        $eventMock = $this->createPartialMock(Event::class, ['getOrder', 'getQuote']);
        $this->observerMock->expects($this->atLeastOnce())
            ->method('getEvent')
            ->willReturn($eventMock);
        $this->orderMock = $this->getMockBuilder(Order::class)->disableOriginalConstructor()->getMock();
        $this->quoteMock = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMpOrderAttributes', 'setMpOrderAttributes', 'getId', 'getStoreId'])
            ->getMock();
        $quoteId = 1;
        $this->quoteMock->expects($this->once())->method('getId')->willReturn($quoteId);
        $eventMock->expects($this->once())->method('getOrder')->willReturn($this->orderMock);
        $eventMock->expects($this->once())->method('getQuote')->willReturn($this->quoteMock);

        $this->quoteAttributeMock = $this->getMockBuilder(QuoteAttribute::class)
            ->disableOriginalConstructor()->getMock();
        $this->quoteAttributeFactoryMock->expects($this->once())
            ->method('create')->willReturn($this->quoteAttributeMock);
        $this->quoteAttributeMock->expects($this->once())->method('load')->with($quoteId)->willReturnSelf();
    }

    public function testExecuteWithFrontendArea()
    {
        $this->quoteAttributeMock->expects($this->once())->method('getId')->willReturn(1);
        $this->helperDataMock->expects($this->at(0))
            ->method('isArea')->with(Area::AREA_GRAPHQL)->willReturn(0);

        $this->helperDataMock->expects($this->at(1))
            ->method('isArea')->with(Area::AREA_ADMINHTML)->willReturn(0);
        $quoteSubmitAttributes = ['my_attribute' => 'Test'];
        $this->quoteMock->expects($this->once())->method('getMpOrderAttributes')->willReturn($quoteSubmitAttributes);
        $result = ['my_attribute' => 'Test', 'my_attribute_label' => __('label')];
        $this->helperDataMock->expects($this->once())->method('validateAttributes')
            ->with($this->quoteMock, $quoteSubmitAttributes, $this->quoteAttributeMock)
            ->willReturn($result);
        $this->orderMock->expects($this->once())->method('addData')->with($result);

        $this->quoteSubmitBefore->execute($this->observerMock);
    }

    public function testExecuteWithGraphQlArea()
    {
        $this->quoteAttributeMock->expects($this->once())->method('getId')->willReturn(1);
        $this->helperDataMock->expects($this->once())
            ->method('isArea')->with(Area::AREA_GRAPHQL)->willReturn(1);

        $data = ['my_attribute' => 'Test'];
        $this->quoteAttributeMock->expects($this->once())->method('getData')->willReturn($data);

        $result = ['my_attribute' => 'Test', 'my_attribute_label' => __('label')];
        $storeId = 1;
        $this->quoteMock->expects($this->once())->method('getStoreId')->willReturn($storeId);
        $this->helperDataMock->expects($this->once())->method('prepareAttributes')
            ->with($storeId, $data)
            ->willReturn($result);
        $this->orderMock->expects($this->once())->method('addData')->with($result);

        $this->quoteSubmitBefore->execute($this->observerMock);
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Zend_Validate_Exception
     */
    public function testExecuteWithAdminAreaWithException()
    {
        $this->quoteAttributeMock->expects($this->once())->method('getId')->willReturn(1);
        $this->helperDataMock->expects($this->at(0))
            ->method('isArea')->with(Area::AREA_GRAPHQL)->willReturn(0);
        $this->helperDataMock->expects($this->at(1))
            ->method('isArea')->with(Area::AREA_ADMINHTML)->willReturn(1);

        $data = [
            'mpOrderAttributes' => [
                'my_attribute' => 'test',
            ]
        ];
        $this->requestMock->expects($this->once())->method('getPostValue')->willReturn($data);

        $input = [
            'mpOrderAttributes' => [
                'name' => ['my_file' => 'test.zip'],
                'type' => ['my_file' => 'application/zip'],
                'tmp_name' => ['my_file' => '/tmp/phpKcnvo5'],
                'error' => ['my_file' => 0],
                'size' => ['my_file' => 136852]
            ]
        ];
        $this->fileUploadMock->expects($this->once())->method('getFiles')->willReturn($input);

        $fileUploaded = new FileResult(['error' => __('Test LocalizedException')]);
        $fileFormat = [
            'my_file' => [
                'name' => 'test.zip',
                'type' => 'application/zip',
                'tmp_name' => '/tmp/phpKcnvo5',
                'error' => 0,
                'size' => 136852
            ]
        ];
        $this->attributeRepositoryMock->expects($this->once())
            ->method('uploadFile')
            ->with('my_file', $fileFormat)
            ->willReturn($fileUploaded);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Test LocalizedException');

        $this->quoteSubmitBefore->execute($this->observerMock);
    }

    /**
     * @param array $data
     * @param array $files
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Zend_Validate_Exception
     * @dataProvider providerExecuteWithAdminArea
     */
    public function testExecuteWithAdminArea($data, $files)
    {
        $this->quoteAttributeMock->expects($this->once())->method('getId')->willReturn(1);
        $this->helperDataMock->expects($this->at(0))
            ->method('isArea')->with(Area::AREA_GRAPHQL)->willReturn(0);
        $this->helperDataMock->expects($this->at(1))
            ->method('isArea')->with(Area::AREA_ADMINHTML)->willReturn(1);

        $this->requestMock->expects($this->once())->method('getPostValue')->willReturn($data);
        $result = $data['mpOrderAttributes'];
        if ($files) {
            $this->fileUploadMock->expects($this->once())->method('getFiles')->willReturn($files['input']);

            $fileUploaded = $files['result'];
            $this->attributeRepositoryMock->expects($this->once())
                ->method('uploadFile')
                ->with('my_file', $files['file_format'])
                ->willReturn($fileUploaded);

            $this->helperDataMock->expects($this->once())->method('jsonEncodeData')
                ->with($fileUploaded->getData())
                ->willReturn($files['result_encode']);
            $this->quoteAttributeMock->expects($this->once())
                ->method('setData')->with('my_file', $files['result_encode']);

            $result['my_file'] = $files['result_encode'];
        }
        $this->quoteMock->expects($this->once())->method('setMpOrderAttributes')->with($result);

        $this->quoteMock->expects($this->once())->method('getMpOrderAttributes')
            ->willReturn($result);

        $this->helperDataMock->expects($this->once())->method('validateAttributes')
            ->with($this->quoteMock, $result, $this->quoteAttributeMock)
            ->willReturn($result);
        $this->orderMock->expects($this->once())->method('addData')->with($result);

        $this->quoteSubmitBefore->execute($this->observerMock);
    }

    /**
     * @return array
     */
    public function providerExecuteWithAdminArea()
    {
        $files = [
            'input' => [
                'mpOrderAttributes' => [
                    'name' => ['my_file' => 'test.zip'],
                    'type' => ['my_file' => 'application/zip'],
                    'tmp_name' => ['my_file' => '/tmp/phpKcnvo5'],
                    'error' => ['my_file' => 0],
                    'size' => ['my_file' => 136852]
                ]
            ],
            'file_format' => [
                'my_file' => [
                    'name' => 'test.zip',
                    'type' => 'application/zip',
                    'tmp_name' => '/tmp/phpKcnvo5',
                    'error' => 0,
                    'size' => 136852
                ]
            ],
            'result' => new FileResult(
                [
                    'type' => 'application/zip',
                    'error' => 0,
                    'name' => 'test.zip',
                    'file' => '/d/b/test.zip',
                    'url' => 'https://mydomain.com/pub/media/mageplaza/order_attributes/tmp/d/b//d/b/test.zip'
                ]
            ),
            'result_encode' => '{"type":"application\/zip","error":0,"name":"test.zip","file":"\/d\/b\/test.zip","url":"https:\/\/mydomain.com\/pub\/media\/mageplaza\/order_attributes\/tmp\/d\/b\/\/d\/b\/test.zip"}'
        ];

        return [
            [
                [
                    'mpOrderAttributes' => [
                        'my_attribute' => 'test',
                    ]
                ],
                []
            ],
            [
                [
                    'mpOrderAttributes' => [
                        'my_attribute' => 'test',
                    ]
                ],
                $files
            ]
        ];
    }
}
