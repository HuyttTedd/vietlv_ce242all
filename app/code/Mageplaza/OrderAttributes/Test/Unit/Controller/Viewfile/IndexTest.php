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

namespace Mageplaza\OrderAttributes\Test\Unit\Controller\Viewfile;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Url\DecoderInterface;
use Magento\MediaStorage\Helper\File\Storage;
use Mageplaza\OrderAttributes\Controller\Viewfile\Index;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class IndexTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Controller\Viewfile
 */
class IndexTest extends TestCase
{
    /**
     * @var RawFactory|MockObject
     */
    private $resultRawFactoryMock;

    /**
     * @var DecoderInterface|MockObject
     */
    private $urlDecoderMock;

    /**
     * @var FileFactory|MockObject
     */
    private $fileFactoryMock;

    /**
     * @var Filesystem|MockObject
     */
    private $filesystemMock;

    /**
     * @var Storage|MockObject
     */
    private $storageMock;

    private $requestMock;

    /**
     * @var Index
     */
    private $indexController;

    public function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $this->resultRawFactoryMock = $this->getMockBuilder(RawFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->urlDecoderMock = $this->getMockBuilder(DecoderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->fileFactoryMock = $this->getMockBuilder(FileFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->filesystemMock = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->storageMock = $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->indexController = new Index(
            $contextMock,
            $this->resultRawFactoryMock,
            $this->urlDecoderMock,
            $this->fileFactoryMock,
            $this->filesystemMock,
            $this->storageMock
        );
    }

    public function testExecuteWithEmptyParamException()
    {
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->withConsecutive(['file'], ['image'])
            ->willReturnOnConsecutiveCalls(null, null);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Page not found.');

        $this->indexController->execute();
    }

    /**
     * @return array
     */
    public function providerTestExecuteWithProcessFileAndImageException()
    {
        return [
            [
                'L3MvYy9zY3JlZW5zaG90X2Zyb21fMjAxOS0xMi0yN18xNC00NS0xMF8xLnBuZw,,',
                '/s/c/screenshot_from_2019-12-27_14-45-10_1.png',
                '',
                '',
                'mageplaza/order_attributes/s/c/screenshot_from_2019-12-27_14-45-10_1.png'
            ],
            [
                '',
                '',
                'L3MvYy9zY3JlZW5zaG90X2Zyb21fMjAxOS0xMi0yN18wOS01Ni0wMF8xLnBuZw,,',
                '/s/c/screenshot_from_2019-12-30_16-55-12_1.png',
                'mageplaza/order_attributes/s/c/screenshot_from_2019-12-30_16-55-12_1.png'
            ]
        ];
    }

    /**
     * @param string $fileToken
     * @param string $fileDecode
     * @param string $imageToken
     * @param string $imageDecode
     * @param string $fileName
     *
     * @throws FileSystemException
     * @throws NotFoundException
     * @dataProvider providerTestExecuteWithProcessFileAndImageException
     */
    public function testExecuteWithProcessFileAndImageException(
        $fileToken,
        $fileDecode,
        $imageToken,
        $imageDecode,
        $fileName
    ) {
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->withConsecutive(['file'], ['image'])
            ->willReturnOnConsecutiveCalls($fileToken, $imageToken);

        if ($imageDecode) {
            $fileToken = $imageToken;
            $fileDecode = $imageDecode;
        }

        $this->urlDecoderMock->expects($this->once())
            ->method('decode')
            ->with($fileToken)
            ->willReturn($fileDecode);

        $directoryRead = $this->getMockBuilder(ReadInterface::class)->getMock();
        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)->willReturn($directoryRead);
        $directoryRead->expects($this->once())
            ->method('getAbsolutePath')
            ->with($fileName)
            ->willReturn('some_path');
        $directoryRead->expects($this->once())
            ->method('isFile')
            ->with($fileName)
            ->willReturn(false);
        $this->storageMock->expects($this->once())
            ->method('processStorageFile')
            ->with('some_path')
            ->willReturn(false);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Page not found.');

        $this->indexController->execute();
    }

    public function testExecuteWithFile()
    {
        $fileToken = 'L3MvYy9zY3JlZW5zaG90X2Zyb21fMjAxOS0xMi0yN18xNC00NS0xMF8xLnBuZw,,';
        $fileDecode = '/s/c/screenshot_from_2019-12-27_14-45-10_1.png';
        $fileName = 'mageplaza/order_attributes/s/c/screenshot_from_2019-12-27_14-45-10_1.png';
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('file')
            ->willReturn($fileToken);

        $this->urlDecoderMock->expects($this->once())
            ->method('decode')
            ->with($fileToken)
            ->willReturn($fileDecode);

        $directoryRead = $this->getMockBuilder(ReadInterface::class)->getMock();
        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)->willReturn($directoryRead);
        $directoryRead->expects($this->once())
            ->method('getAbsolutePath')
            ->with($fileName)
            ->willReturn('some_path//s/c/screenshot_from_2019-12-27_14-45-10_1.png');
        $directoryRead->expects($this->once())
            ->method('isFile')
            ->with($fileName)
            ->willReturn(false);
        $this->storageMock->expects($this->once())
            ->method('processStorageFile')
            ->with('some_path//s/c/screenshot_from_2019-12-27_14-45-10_1.png')
            ->willReturn(true);

        $this->fileFactoryMock->expects($this->once())
            ->method('create')
            ->with(
                'screenshot_from_2019-12-27_14-45-10_1.png',
                ['type' => 'filename', 'value' => $fileName],
                DirectoryList::MEDIA
            );

        $this->indexController->execute();
    }

    /**
     * @return array
     */
    public function providerTestExecuteWithImage()
    {
        return [
            ['gif', 'image/gif'],
            ['jpg', 'image/jpeg'],
            ['png', 'image/png'],
            ['gz', 'application/octet-stream']

        ];
    }

    /**
     * @param string $extension
     * @param string $contentType
     *
     * @throws FileSystemException
     * @throws NotFoundException
     * @dataProvider providerTestExecuteWithImage
     */
    public function testExecuteWithImage($extension, $contentType)
    {
        $path = 'some_path//s/c/screenshot_from_2019-12-27_14-45-10_1.' . $extension;
        $imageToken = 'L3MvYy9zY3JlZW5zaG90X2Zyb21fMjAxOS0xMi0yN18xNC00NS0xMF8xLnBuZw,,';
        $imageDecode = '/s/c/screenshot_from_2019-12-27_14-45-10_1.' . $extension;
        $fileName = 'mageplaza/order_attributes/s/c/screenshot_from_2019-12-27_14-45-10_1.' . $extension;

        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->withConsecutive(['file'], ['image'])
            ->willReturnOnConsecutiveCalls('', $imageToken);

        $this->urlDecoderMock->expects($this->once())
            ->method('decode')
            ->with($imageToken)
            ->willReturn($imageDecode);

        $directoryRead = $this->getMockBuilder(ReadInterface::class)->getMock();
        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)->willReturn($directoryRead);
        $directoryRead->expects($this->once())
            ->method('getAbsolutePath')
            ->with($fileName)
            ->willReturn($path);
        $directoryRead->expects($this->once())
            ->method('isFile')
            ->with($fileName)
            ->willReturn(false);
        $this->storageMock->expects($this->once())
            ->method('processStorageFile')
            ->with($path)
            ->willReturn(true);

        $resultRawMock = $this->getMockBuilder(Raw::class)
            ->disableOriginalConstructor()->getMock();
        $resultRawMock->expects($this->once())->method('setHttpResponseCode')->with(200)->willReturnSelf();
        $contentLength = 11;
        $contentModify = 'Wed, 06 May 2020 01:59:32 -0700';
        $resultRawMock->expects($this->exactly(4))
            ->method('setHeader')
            ->withConsecutive(
                ['Pragma', 'public', true],
                ['Content-type', $contentType],
                ['Content-Length', $contentLength],
                ['Last-Modified', $contentModify]
            )->willReturnSelf();
        $directoryRead->expects($this->once())
            ->method('stat')
            ->with($fileName)
            ->willReturn(
                ['size' => $contentLength, 'mtime' => '1588755572']
            );
        $directoryRead->expects($this->once())->method('readFile')->with($fileName)->willReturn('test');
        $resultRawMock->expects($this->once())->method('setContents')->with('test')->willReturnSelf();

        $this->resultRawFactoryMock->expects($this->once())->method('create')->willReturn($resultRawMock);

        $this->indexController->execute();
    }
}
