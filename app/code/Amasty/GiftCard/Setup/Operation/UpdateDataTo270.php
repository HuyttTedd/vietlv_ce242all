<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Setup\Operation;

use Amasty\GiftCard\Api\Data\ImageElementsInterface;
use Amasty\GiftCard\Api\Data\ImageInterface;
use Amasty\GiftCard\Model\Image\ResourceModel\Image;
use Amasty\GiftCard\Model\Image\ResourceModel\ImageElements;
use Amasty\GiftCard\Utils\FileUpload;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpdateDataTo270
{
    public const WIDTH_KEY = 'width';
    public const HEIGHT_KEY = 'height';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var File
     */
    private $ioFile;

    public function __construct(
        Filesystem $filesystem,
        File $ioFile
    ) {
        $this->filesystem = $filesystem;
        $this->ioFile = $ioFile;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     *
     * @throws \Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup)
    {
        $setup->startSetup();

        $imageTable = $setup->getTable(Image::TABLE_NAME);
        $imageElementsTable = $setup->getTable(ImageElements::TABLE_NAME);

        $select = $setup->getConnection()->select()
            ->from(['img' => $imageTable])
            ->joinLeft(
                ['img_el' => $imageElementsTable],
                'img.' . ImageInterface::IMAGE_ID . ' = img_el.' . ImageElementsInterface::IMAGE_ID
            );
        $allElementsRecords = $setup->getConnection()->fetchAll($select);
        $existingImageParams = $this->getExistingImageParams();

        if ($insertData = $this->getDataToInsert($allElementsRecords, $existingImageParams)) {
            $setup->getConnection()->insertOnDuplicate($imageElementsTable, $insertData);
        }

        $setup->endSetup();
    }

    private function getExistingImageParams(): array
    {
        $result = [];
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $imagesPath = FileUpload::AMGIFTCARD_IMAGE_MEDIA_PATH . DIRECTORY_SEPARATOR . FileUpload::ADMIN_UPLOAD_PATH;

        if ($mediaDirectory->isExist($imagesPath)) {
            $this->ioFile->cd($mediaDirectory->getAbsolutePath($imagesPath));
            foreach ($this->ioFile->ls() as $imageFile) {
                if (!$imageFile['is_image']) {
                    continue;
                }
                $name = $imageFile['text'];
                //phpcs:ignore Magento2.Functions.DiscouragedFunction.DiscouragedWithAlternative
                $imageInfo = getimagesize($mediaDirectory->getAbsolutePath($imagesPath) . DIRECTORY_SEPARATOR . $name);
                $result[$name] = [
                    self::WIDTH_KEY => $imageInfo[0],
                    self::HEIGHT_KEY => $imageInfo[1]
                ];
            }
        }

        return $result;
    }

    private function getDataToInsert(array $allElementsRecords, array $existingImageParams): array
    {
        $insertData = [];

        foreach ($allElementsRecords as $record) {
            $imageName = $record[ImageInterface::IMAGE_PATH];
            if (isset($existingImageParams[$imageName])) {
                $originalWidth = $existingImageParams[$imageName][self::WIDTH_KEY];
                $originalHeight = $existingImageParams[$imageName][self::HEIGHT_KEY];

                if ($originalWidth <= ImageInterface::DEFAULT_WIDTH
                    && $originalHeight <= ImageInterface::DEFAULT_HEIGHT
                ) {
                    continue;
                }
                $posX = $originalWidth > ImageInterface::DEFAULT_WIDTH
                    ? $record[ImageElementsInterface::POS_X] / ($originalWidth / ImageInterface::DEFAULT_WIDTH)
                    : $record[ImageElementsInterface::POS_X];
                $posY = $originalHeight > ImageInterface::DEFAULT_HEIGHT
                    ? $record[ImageElementsInterface::POS_Y] / ($originalHeight / ImageInterface::DEFAULT_HEIGHT)
                    : $record[ImageElementsInterface::POS_Y];

                $insertData[] = [
                    ImageElementsInterface::ELEMENT_ID => $record[ImageElementsInterface::ELEMENT_ID],
                    ImageElementsInterface::POS_X => ceil($posX),
                    ImageElementsInterface::POS_Y => ceil($posY)
                ];
            }
        }

        return $insertData;
    }
}
