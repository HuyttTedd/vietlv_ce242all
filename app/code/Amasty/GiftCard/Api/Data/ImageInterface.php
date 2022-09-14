<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/


namespace Amasty\GiftCard\Api\Data;

interface ImageInterface
{
    public const IMAGE_ID = 'image_id';
    public const TITLE = 'title';
    public const GCARD_TITLE = 'gcard_title';
    public const STATUS = 'status';
    public const WIDTH = 'width';
    public const HEIGHT = 'height';
    public const IMAGE_PATH = 'image_path';
    public const IS_USER_UPLOAD = 'user_upload';
    public const IMAGE_ELEMENTS = 'image_elements';
    public const BAKING_INFO = 'baking_info';

    public const DEFAULT_WIDTH = 580;
    public const DEFAULT_HEIGHT = 390;

    /**
     * @return int
     */
    public function getImageId(): int;

    /**
     * @param int $imageId
     *
     * @return \Amasty\GiftCard\Api\Data\ImageInterface
     */
    public function setImageId(int $imageId): ImageInterface;

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @param string $title
     *
     * @return \Amasty\GiftCard\Api\Data\ImageInterface
     */
    public function setTitle(string $title): ImageInterface;

    /**
     * @return string
     */
    public function getGcardTitle(): ?string;

    /**
     * @param string $title
     *
     * @return \Amasty\GiftCard\Api\Data\ImageInterface
     */
    public function setGcardTitle(string $title): ImageInterface;

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @param int $status
     *
     * @return \Amasty\GiftCard\Api\Data\ImageInterface
     */
    public function setStatus(int $status): ImageInterface;

    /**
     * @return int
     */
    public function getWidth(): int;

    /**
     * @param int $width
     * @return \Amasty\GiftCard\Api\Data\ImageInterface
     */
    public function setWidth(int $width): ImageInterface;

    /**
     * @return int
     */
    public function getHeight(): int;

    /**
     * @param int $height
     * @return \Amasty\GiftCard\Api\Data\ImageInterface
     */
    public function setHeight(int $height): ImageInterface;

    /**
     * @return string|null
     */
    public function getImagePath();

    /**
     * @param string|null $imagePath
     *
     * @return \Amasty\GiftCard\Api\Data\ImageInterface
     */
    public function setImagePath($imagePath): ImageInterface;

    /**
     * @return bool
     */
    public function isUserUpload(): bool;

    /**
     * @param bool $flag
     *
     * @return \Amasty\GiftCard\Api\Data\ImageInterface
     */
    public function setIsUserUpload(bool $flag): ImageInterface;

    /**
     * @return \Amasty\GiftCard\Api\Data\ImageElementsInterface[]
     */
    public function getImageElements(): array;

    /**
     * @param \Amasty\GiftCard\Api\Data\ImageElementsInterface[] $elements
     * @return \Amasty\GiftCard\Api\Data\ImageInterface
     */
    public function setImageElements(array $elements): ImageInterface;

    /**
     * @deprecated
     * @since 2.8.0
     *
     * @return \Amasty\GiftCard\Api\Data\ImageBakingInfoInterface[]
     */
    public function getBakingInfo(): array;

    /**
     * @deprecated
     * @since 2.8.0
     *
     * @param \Amasty\GiftCard\Api\Data\ImageBakingInfoInterface[] $backingInfo
     * @return \Amasty\GiftCard\Api\Data\ImageInterface
     *
     */
    public function setBakingInfo(array $backingInfo): ImageInterface;
}
