<?php

namespace Amasty\PushNotifications\Model\FileUploader;

use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Theme\Model\Design\Config\MetadataProvider;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Model\Design\Backend\Image;
use Magento\Framework\Filesystem\DriverInterface;
use Amasty\Base\Model\MagentoVersion;

class FileProcessor
{
    /**
     * @var string
     */
    const FILE_DIR = 'amasty/push_notifications';

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var MetadataProvider
     */
    private $metadataProvider;

    /**
     * Media Directory object (writable).
     *
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Image
     */
    private $imageBackendModel;

    /**
     * @var File
     */
    private $file;

    /**
     * @var MagentoVersion
     */
    private $magentoVersion;

    /**
     * @var array
     */
    private $allowedExtensions = ['jpg', 'jpeg', 'gif', 'png', 'svg'];

    public function __construct(
        UploaderFactory $uploaderFactory,
        MetadataProvider $metadataProvider,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        Image $imageBackendModel,
        File $file,
        MagentoVersion $magentoVersion
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->metadataProvider = $metadataProvider;
        $this->storeManager = $storeManager;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->imageBackendModel = $imageBackendModel;
        $this->file = $file;
        $this->magentoVersion = $magentoVersion;
    }

    /**
     * Save file to temp media directory
     *
     * @param  string $fileId
     * @return array
     * @throws LocalizedException
     */
    public function saveToTmp($fileId)
    {
        try {
            $result = $this->save($fileId, $this->getAbsoluteTmpMediaPath());
            $result['url'] = $this->getTmpMediaUrl($result['file']);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $result;
    }

    /**
     * Save temp file to media directory
     *
     * @param  array $file
     * @param  string|int $campaignId
     *
     * @return string
     *
     * @throws \Exception
     */
    public function saveTmp($file, $campaignId)
    {
        $fileName = $file['file'];
        $isNeedAbsolutePath = (bool)version_compare($this->magentoVersion->get(), '2.2', '>=');

        $destinationPath = $isNeedAbsolutePath ?
            $this->getAbsoluteLogoMediaPath($campaignId) : $this->getLogoMediaPath($campaignId);
        $tmpFilePath = $isNeedAbsolutePath ?
            $this->getAbsoluteTmpFilePath($file['file']) : $this->getTmpFilePath($file['file']);

        $this->validateDestination($destinationPath);

        if ($this->mediaDirectory->isExist($tmpFilePath)) {
            $uploadPath = $isNeedAbsolutePath ? $this->getFullPathToUploadedFile($campaignId, $fileName)
                : $this->getPathToUploadedFile($campaignId, $fileName);

            $result = $this->moveFile(
                $tmpFilePath,
                $uploadPath
            );

            if ($result) {
                return $this->getShortLogoMediaPath($campaignId, $fileName);
            }
        }

        return '';
    }

    /**
     * @param string|int $campaignId
     * @param string $fileName
     * @return string
     */
    private function getFullPathToUploadedFile($campaignId, $fileName)
    {
        return $this->getAbsoluteLogoMediaPath($campaignId)
            . DIRECTORY_SEPARATOR . $this->prepareFile($fileName);
    }

    /**
     * @param string|int $campaignId
     * @param string $fileName
     * @return string
     */
    private function getPathToUploadedFile($campaignId, $fileName)
    {
        return $this->getLogoMediaPath($campaignId)
            . DIRECTORY_SEPARATOR . $this->prepareFile($fileName);
    }

    /**
     * @param string $file
     * @param int|string $campaignId
     * @return string
     */
    private function getLogoMediaUrl($file, $campaignId)
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
            . self::FILE_DIR . DIRECTORY_SEPARATOR . CampaignInterface::CAMPAIGN_ID . DIRECTORY_SEPARATOR
            . $campaignId . DIRECTORY_SEPARATOR . $this->prepareFile($file);
    }

    /**
     * Retrieve temp media url
     *
     * @param string $file
     * @return string
     */
    private function getTmpMediaUrl($file)
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
            . 'tmp/' . self::FILE_DIR . '/' . $this->prepareFile($file);
    }

    /**
     * Prepare file
     *
     * @param string $file
     * @return string
     */
    private function prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }

    /**
     * @param string|int $campaignId
     * @param string $file
     * @return string
     */
    private function getAbsoluteLogoMediaPath($campaignId)
    {
        return $this->mediaDirectory->getAbsolutePath(
            self::FILE_DIR . DIRECTORY_SEPARATOR . CampaignInterface::CAMPAIGN_ID . DIRECTORY_SEPARATOR . $campaignId
        );
    }

    /**
     * @param string|int $campaignId
     * @param string $file
     * @return string
     */
    private function getLogoMediaPath($campaignId)
    {
        return self::FILE_DIR . DIRECTORY_SEPARATOR
            . CampaignInterface::CAMPAIGN_ID . DIRECTORY_SEPARATOR . $campaignId;
    }

    /**
     * @param string|int $campaignId
     * @param string $file
     * @return string
     */
    private function getShortLogoMediaPath($campaignId, $file)
    {
        return DIRECTORY_SEPARATOR . CampaignInterface::CAMPAIGN_ID . DIRECTORY_SEPARATOR . $campaignId
            . DIRECTORY_SEPARATOR . $this->prepareFile($file);
    }

    /**
     * Retrieve absolute temp media path
     *
     * @return string
     */
    private function getAbsoluteTmpMediaPath()
    {
        return $this->mediaDirectory->getAbsolutePath('tmp/' . self::FILE_DIR);
    }

    /**
     * @param $file
     * @return string
     */
    private function getAbsoluteTmpFilePath($file)
    {
        return $this->mediaDirectory->getAbsolutePath(
            'tmp/' . self::FILE_DIR . DIRECTORY_SEPARATOR . $file
        );
    }

    /**
     * @param $file
     * @return string
     */
    private function getTmpFilePath($file)
    {
        return 'tmp/' . self::FILE_DIR . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * Move files from TMP folder into destination folder
     *
     * @param string $tmpPath
     * @param string $destPath
     * @return bool
     */
    private function moveFile($tmpPath, $destPath)
    {
        return $this->mediaDirectory->renameFile($tmpPath, $destPath);
    }

    /**
     * Save image
     *
     * @param string $fileId
     * @param string $destination
     * @return array
     * @throws LocalizedException
     */
    private function save($fileId, $destination)
    {
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);
        $uploader->setAllowedExtensions($this->allowedExtensions);
        $uploader->addValidateCallback('size', $this->imageBackendModel, 'validateMaxSize');

        $result = $uploader->save($destination);
        unset($result['path']);

        return $result;
    }

    /**
     * Validates destination directory to be writable
     *
     * @param string $destinationFolder
     * @return void
     * @throws \Exception
     */
    private function validateDestination($destinationFolder)
    {
        $this->createDestinationFolder($destinationFolder);

        if (!$this->mediaDirectory->isWritable($destinationFolder)) {
            throw new LocalizedException('Destination folder is not writable or does not exists.');
        }
    }

    /**
     * Create destination folder
     *
     * @param string $destinationFolder
     * @return \Magento\Framework\File\Uploader
     * @throws LocalizedException
     */
    private function createDestinationFolder($destinationFolder)
    {
        if (!$destinationFolder) {
            return $this;
        }

        if (substr($destinationFolder, -1) == '/') {
            $destinationFolder = substr($destinationFolder, 0, -1);
        }

        if (!($this->file->isDirectory($destinationFolder)
            || $this->mediaDirectory->create($destinationFolder)
        )) {
            throw new LocalizedException("Unable to create directory '{$destinationFolder}'.");
        }

        return $this;
    }
}
