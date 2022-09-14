<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Image\Utils;

use Amasty\GiftCard\Api\Data\ImageInterface;
use Amasty\GiftCard\Model\Pdf\PdfImageConverter;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class ImageHtmlToFileConverter
{
    public const DEFAULT_POS = 0;
    public const AMGIFTCARD_RENDERED_IMAGE_MEDIA_PATH = 'amasty/amgcard/image/generated_images_cache';

    /**
     * @var PdfImageConverter
     */
    private $pdfImageConverter;

    /**
     * @var Factory\ImagickFactory
     */
    private $imagickFactory;

    /**
     * @var Filesystem\Directory\ReadInterface
     */
    private $mediaDir;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var File
     */
    private $ioFile;

    public function __construct(
        PdfImageConverter $pdfImageConverter,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        File $ioFile,
        Factory\ImagickFactory $imagickFactory
    ) {
        $this->imagickFactory = $imagickFactory;
        $this->pdfImageConverter = $pdfImageConverter;
        $this->mediaDir = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->storeManager = $storeManager;
        $this->ioFile = $ioFile;
    }

    /**
     * Trying to convert image from HTML string to file for img src attribute
     * for better compatibility with different email clients.
     * Return original HTML in case of error.
     *
     * @param ImageInterface $image
     * @param string $code
     * @param string $html
     * @return string
     */
    public function convert(ImageInterface $image, string $code, string $html): string
    {
        try {
            if ($imageName = $this->createImageFile($image, $code, $html)) {
                $imagePath = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                    . self::AMGIFTCARD_RENDERED_IMAGE_MEDIA_PATH
                    . DIRECTORY_SEPARATOR . $imageName;

                return '<img src="' . $imagePath . '" />';
            }
        } catch (\Exception $e) {//phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
            //no need to process exception
        }

        return $html;
    }

    public function createImageFile(ImageInterface $image, string $code, string $html): string
    {
        if (!($img = $this->imagickFactory->create())
            || !($pdfString = $this->pdfImageConverter->convert($html, $code))
        ) {
            return '';
        }

        $extension = '.' . $this->ioFile->getPathInfo($image->getImagePath())['extension'];
        $filename = $code . $extension;
        $mediaPath = self::AMGIFTCARD_RENDERED_IMAGE_MEDIA_PATH . DIRECTORY_SEPARATOR . $filename;
        if ($this->mediaDir->isExist($mediaPath)) {
            //regenerate image because of possible changes
            $this->mediaDir->delete($mediaPath);
        }
        $absolutePath = $this->mediaDir->getAbsolutePath($mediaPath);

        $img->setSize($image->getWidth(), $image->getHeight());
        $img->readImageBlob($pdfString);
        $img->cropImage($image->getWidth(), $image->getHeight(), self::DEFAULT_POS, self::DEFAULT_POS);
        if (!$this->mediaDir->isExist(self::AMGIFTCARD_RENDERED_IMAGE_MEDIA_PATH)) {
            $this->mediaDir->create(self::AMGIFTCARD_RENDERED_IMAGE_MEDIA_PATH);
        }
        $img->writeImage($absolutePath);

        return $filename;
    }
}
