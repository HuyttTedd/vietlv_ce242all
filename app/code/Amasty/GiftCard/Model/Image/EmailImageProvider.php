<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Image;

use Amasty\GiftCard\Api\Data\ImageInterface;
use Amasty\GiftCard\Model\Image\Utils\ImageHtmlToFileConverter;
use Amasty\GiftCard\Utils\FileUpload;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem\Io\File;

class EmailImageProvider
{
    /**
     * @var FileUpload
     */
    private $fileUpload;

    /**
     * @var File
     */
    private $file;

    /**
     * @var OutputBuilderFactory
     */
    private $outputBuilderFactory;

    /**
     * @var Mime
     */
    private $mime;

    /**
     * @var ImageHtmlToFileConverter
     */
    private $imageHtmlToFileConverter;

    public function __construct(
        FileUpload $fileUpload,
        File $file,
        OutputBuilderFactory $outputBuilderFactory,
        Mime $mime,
        ImageHtmlToFileConverter $imageHtmlToFileConverter
    ) {
        $this->fileUpload = $fileUpload;
        $this->file = $file;
        $this->outputBuilderFactory = $outputBuilderFactory;
        $this->mime = $mime;
        $this->imageHtmlToFileConverter = $imageHtmlToFileConverter;
    }

    public function get(ImageInterface $image, string $code): string
    {
        $imagePath = $this->fileUpload->getImagePath($image);
        if (!$this->file->fileExists($imagePath)) {
            return '';
        }

        $fileContent = $this->file->read($imagePath);
        $result = '<div style="overflow:hidden;position:relative;height:' . $image->getHeight()
            . 'px;width:' . $image->getWidth() . 'px;"><img style="height:100%;width:100%;" src="data:'
            . $this->mime->getMimeType($imagePath)
            . ';base64,' . base64_encode($fileContent) . '">';

        if (!$image->isUserUpload() && $image->getImageElements()) {
            $result .= $this->outputBuilderFactory->create(OutputBuilderFactory::HTML_BUILDER, ['code' => $code])
                ->build($image->getImageElements());
        }
        $result .= '</div>';

        return $this->imageHtmlToFileConverter->convert($image, $code, $result);
    }
}
