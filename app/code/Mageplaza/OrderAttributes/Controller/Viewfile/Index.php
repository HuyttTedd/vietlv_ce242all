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

namespace Mageplaza\OrderAttributes\Controller\Viewfile;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filesystem;
use Magento\Framework\Url\DecoderInterface;
use Magento\MediaStorage\Helper\File\Storage;
use Mageplaza\OrderAttributes\Helper\Data;

/**
 * Class Index
 * @package Mageplaza\OrderAttributes\Controller\Viewfile
 */
class Index extends Action
{
    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var DecoderInterface
     */
    protected $urlDecoder;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Storage
     */
    protected $storage;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param RawFactory $resultRawFactory
     * @param DecoderInterface $urlDecoder
     * @param FileFactory $fileFactory
     * @param Filesystem $filesystem
     * @param Storage $storage
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        DecoderInterface $urlDecoder,
        FileFactory $fileFactory,
        Filesystem $filesystem,
        Storage $storage
    ) {
        $this->fileFactory = $fileFactory;
        $this->urlDecoder = $urlDecoder;
        $this->resultRawFactory = $resultRawFactory;
        $this->filesystem = $filesystem;
        $this->storage = $storage;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Raw|ResultInterface
     * @throws NotFoundException
     * @throws FileSystemException
     * @throws Exception
     */
    public function execute()
    {
        list($file, $plain) = $this->getFileParams();

        $directory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $fileName = Data::TEMPLATE_MEDIA_PATH . '/' . ltrim($file, '/');
        $path = $directory->getAbsolutePath($fileName);
        if (mb_strpos($path, '..') !== false
            || (!$directory->isFile($fileName) && !$this->storage->processStorageFile($path))
        ) {
            throw new NotFoundException(__('Page not found.'));
        }

        if ($plain) {
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            switch (strtolower($extension)) {
                case 'gif':
                    $contentType = 'image/gif';
                    break;
                case 'jpg':
                    $contentType = 'image/jpeg';
                    break;
                case 'png':
                    $contentType = 'image/png';
                    break;
                default:
                    $contentType = 'application/octet-stream';
                    break;
            }
            $stat = $directory->stat($fileName);
            $contentLength = $stat['size'];
            $contentModify = $stat['mtime'];

            /** @var Raw $resultRaw */
            $resultRaw = $this->resultRawFactory->create();
            $resultRaw->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Content-type', $contentType, true)
                ->setHeader('Content-Length', $contentLength)
                ->setHeader('Last-Modified', date('r', $contentModify));
            $resultRaw->setContents($directory->readFile($fileName));

            return $resultRaw;
        }

        $name = pathinfo($path, PATHINFO_BASENAME);

        return $this->fileFactory->create(
            $name,
            ['type' => 'filename', 'value' => $fileName],
            DirectoryList::MEDIA
        );
    }

    /**
     * Get parameters from request.
     *
     * @return array
     * @throws NotFoundException
     */
    private function getFileParams()
    {
        $file = $this->getRequest()->getParam('file');
        if ($file) {
            // download file
            $file = $this->urlDecoder->decode($file);

            return [$file, false];
        }

        $image = $this->getRequest()->getParam('image');
        if ($image) {
            // show plain image
            $file = $this->urlDecoder->decode($image);

            return [$file, true];
        }

        throw new NotFoundException(__('Page not found.'));
    }
}
