<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Api\Data;

interface ImageElementsInterface
{
    public const ELEMENT_ID = 'element_id';
    public const IMAGE_ID = 'image_id';
    public const NAME = 'name';
    public const WIDTH = 'width';
    public const HEIGHT = 'height';
    public const POS_X = 'pos_x';
    public const POS_Y = 'pos_y';
    public const CUSTOM_CSS = 'custom_css';
    public const VALUE_DATA_SOURCE = 'value_data_source';

    /**
     * @return int
     */
    public function getElementId(): int;

    /**
     * @param int $elementId
     * @return \Amasty\GiftCard\Api\Data\ImageElementsInterface
     */
    public function setElementId(int $elementId): ImageElementsInterface;

    /**
     * @return int
     */
    public function getImageId(): int;

    /**
     * @param int $imageId
     * @return \Amasty\GiftCard\Api\Data\ImageElementsInterface
     */
    public function setImageId(int $imageId): ImageElementsInterface;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     * @return \Amasty\GiftCard\Api\Data\ImageElementsInterface
     */
    public function setName(string $name): ImageElementsInterface;

    /**
     * @return int
     */
    public function getWidth(): int;

    /**
     * @param int $width
     * @return \Amasty\GiftCard\Api\Data\ImageElementsInterface
     */
    public function setWidth(int $width): ImageElementsInterface;

    /**
     * @return int
     */
    public function getHeight(): int;

    /**
     * @param int $height
     * @return \Amasty\GiftCard\Api\Data\ImageElementsInterface
     */
    public function setHeight(int $height): ImageElementsInterface;

    /**
     * @return int
     */
    public function getPosX(): int;

    /**
     * @param int $posX
     * @return \Amasty\GiftCard\Api\Data\ImageElementsInterface
     */
    public function setPosX(int $posX): ImageElementsInterface;

    /**
     * @return int
     */
    public function getPosY(): int;

    /**
     * @param int $posY
     * @return \Amasty\GiftCard\Api\Data\ImageElementsInterface
     */
    public function setPosY(int $posY): ImageElementsInterface;

    /**
     * @return string
     */
    public function getCustomCss(): ?string;

    /**
     * @param string $css
     * @return \Amasty\GiftCard\Api\Data\ImageElementsInterface
     */
    public function setCustomCss(string $css): ImageElementsInterface;
}
