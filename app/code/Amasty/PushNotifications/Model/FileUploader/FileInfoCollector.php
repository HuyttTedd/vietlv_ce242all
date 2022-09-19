<?php

namespace Amasty\PushNotifications\Model\FileUploader;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;

class FileInfoCollector
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Mime
     */
    private $mime;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        Filesystem $filesystem,
        UrlInterface $urlBuilder,
        Mime $mime
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->filesystem = $filesystem;
        $this->urlBuilder = $urlBuilder;
        $this->mime = $mime;
    }

    /**
     * @param string $value
     * @return array|null
     */
    public function getInfoByFilePath($value)
    {
        $fileInfo = [];

        if ($value && !is_array($value)) {
            $fileName = $this->getUploadDir() . $value;
            $fileInfo = null;

            if ($this->mediaDirectory->isExist($fileName)) {
                $stat = $this->mediaDirectory->stat($fileName);
                $url = $this->getLogoMediaUrl($value);
                $fileInfo = [
                    [
                        'url' => $url,
                        'file' => $value,
                        'size' => is_array($stat) ? $stat['size'] : 0,
                        'name' => basename($value),
                        'type' => $this->getMimeType($fileName),
                        'exists' => true,
                    ]
                ];
            }
        }

        return $fileInfo;
    }

    /**
     * @param string $filePath
     * @return string
     */
    private function getLogoMediaUrl($filePath)
    {
        return $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA])
            . $this->getUploadDir() . $filePath;
    }

    /**
     * @return string
     */
    private function getUploadDir()
    {
        return FileProcessor::FILE_DIR;
    }

    /**
     * Retrieve MIME type of requested file
     *
     * @param string $fileName
     * @return string
     */
    private function getMimeType($fileName)
    {
        $absoluteFilePath = $this->mediaDirectory->getAbsolutePath($fileName);
        $result = $this->mime->getMimeType($absoluteFilePath);

        return $result;
    }
}
