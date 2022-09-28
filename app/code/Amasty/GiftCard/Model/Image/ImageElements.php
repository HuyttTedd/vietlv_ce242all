<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Image;

use Amasty\GiftCard\Api\Data\ImageElementsInterface;
use Amasty\GiftCard\Api\Data\ImageInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;

class ImageElements extends AbstractModel implements ImageElementsInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel\ImageElements::class);
        $this->setIdFieldName(ImageElementsInterface::ELEMENT_ID);
    }

    public function getElementId(): int
    {
        return (int)$this->_getData(self::ELEMENT_ID);
    }

    public function setElementId(int $elementId): ImageElementsInterface
    {
        return $this->setData(self::ELEMENT_ID, $elementId);
    }

    public function getImageId(): int
    {
        return (int)$this->_getData(self::IMAGE_ID);
    }

    public function setImageId(int $imageId): ImageElementsInterface
    {
        return $this->setData(self::IMAGE_ID, $imageId);
    }

    public function getName(): string
    {
        return (string)$this->_getData(self::NAME);
    }

    public function setName(string $name): ImageElementsInterface
    {
        return $this->setData(self::NAME, $name);
    }

    public function getWidth(): int
    {
        return min((int)$this->_getData(self::WIDTH), ImageInterface::DEFAULT_WIDTH);
    }

    public function setWidth(int $width): ImageElementsInterface
    {
        return $this->setData(self::WIDTH, $width);
    }

    public function getHeight(): int
    {
        return min((int)$this->_getData(self::HEIGHT), ImageInterface::DEFAULT_HEIGHT);
    }

    public function setHeight(int $height): ImageElementsInterface
    {
        return $this->setData(self::HEIGHT, $height);
    }

    public function getPosX(): int
    {
        return (int)$this->_getData(self::POS_X);
    }

    public function setPosX(int $posX): ImageElementsInterface
    {
        return $this->setData(self::POS_X, $posX);
    }

    public function getPosY(): int
    {
        return (int)$this->_getData(self::POS_Y);
    }

    public function setPosY(int $posY): ImageElementsInterface
    {
        return $this->setData(self::POS_Y, $posY);
    }

    public function getCustomCss(): ?string
    {
        return $this->_getData(self::CUSTOM_CSS);
    }

    public function setCustomCss(string $css): ImageElementsInterface
    {
        return $this->setData(self::CUSTOM_CSS, $css);
    }

    public function getValueDataSource(): ?DataObject
    {
        return $this->_getData(self::VALUE_DATA_SOURCE);
    }

    public function setValueDataSource(?DataObject $source): ImageElementsInterface
    {
        return $this->setData(self::VALUE_DATA_SOURCE, $source);
    }
}
