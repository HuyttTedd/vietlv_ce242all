<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Image;

use Amasty\GiftCard\Api\Data\ImageInterface;
use Magento\Framework\Model\AbstractModel;

class Image extends AbstractModel implements ImageInterface
{
    public const DATA_PERSISTOR_KEY = 'amgcard_image';

    protected function _construct()
    {
        $this->_init(ResourceModel\Image::class);
        $this->setIdFieldName(ImageInterface::IMAGE_ID);
    }

    public function getImageId(): int
    {
        return (int)$this->_getData(ImageInterface::IMAGE_ID);
    }

    public function setImageId(int $imageId): ImageInterface
    {
        return $this->setData(ImageInterface::IMAGE_ID, (int)$imageId);
    }

    public function getTitle(): string
    {
        return $this->_getData(ImageInterface::TITLE);
    }

    public function setTitle(string $title): ImageInterface
    {
        return $this->setData(ImageInterface::TITLE, $title);
    }

    public function getGcardTitle(): ?string
    {
        return $this->_getData(ImageInterface::GCARD_TITLE);
    }

    public function setGcardTitle(string $title): ImageInterface
    {
        return $this->setData(ImageInterface::GCARD_TITLE, $title);
    }

    public function getStatus(): int
    {
        return (int)$this->_getData(ImageInterface::STATUS);
    }

    public function setStatus(int $status): ImageInterface
    {
        return $this->setData(ImageInterface::STATUS, (int)$status);
    }

    public function getWidth(): int
    {
        return (int)$this->_getData(ImageInterface::WIDTH);
    }

    public function setWidth(int $width): ImageInterface
    {
        return $this->setData(ImageInterface::WIDTH, $width);
    }

    public function getHeight(): int
    {
        return (int)$this->_getData(ImageInterface::HEIGHT);
    }

    public function setHeight(int $height): ImageInterface
    {
        return $this->setData(ImageInterface::HEIGHT, $height);
    }

    public function getImagePath()
    {
        return $this->_getData(ImageInterface::IMAGE_PATH);
    }

    public function setImagePath($imagePath): ImageInterface
    {
        return $this->setData(ImageInterface::IMAGE_PATH, $imagePath);
    }

    public function isUserUpload(): bool
    {
        return (bool)$this->_getData(ImageInterface::IS_USER_UPLOAD);
    }

    public function setIsUserUpload(bool $flag): ImageInterface
    {
        return $this->setData(ImageInterface::IS_USER_UPLOAD, (bool)$flag);
    }

    public function getImageElements(): array
    {
        return (array)$this->_getData(ImageInterface::IMAGE_ELEMENTS);
    }

    public function setImageElements(array $elements): ImageInterface
    {
        return $this->setData(ImageInterface::IMAGE_ELEMENTS, $elements);
    }

    public function getBakingInfo(): array
    {
        return (array)$this->_getData(ImageInterface::BAKING_INFO);
    }

    public function setBakingInfo(array $backingInfo): ImageInterface
    {
        return $this->setData(ImageInterface::BAKING_INFO, $backingInfo);
    }
}
