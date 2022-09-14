<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Image;

use Amasty\GiftCard\Api\Data\ImageElementsInterface;
use Amasty\GiftCard\Api\Data\ImageElementsInterfaceFactory;
use Amasty\GiftCard\Model\Image\ImageElementProcessors\ImageElementProcessorInterface;

class ImageElementConfig
{
    /**
     * @var ImageElementProcessorInterface
     */
    private $imageElementProcessor;

    /**
     * @var string
     */
    private $code;

    /**
     * @var ImageElementsInterface
     */
    private $defaultElement;

    /**
     * @var string
     */
    private $label;

    /**
     * @var int
     */
    private $sortOrder;

    public function __construct(
        ImageElementsInterfaceFactory $imageElementsFactory,
        ImageElementProcessorInterface $imageElementProcessor,
        string $code,
        int $defaultWidth,
        int $defaultHeight,
        int $defaultPosX,
        int $defaultPosY,
        string $defaultCss = '',
        string $label = '',
        int $sortOrder = 0
    ) {
        $this->code = $code;
        $this->imageElementProcessor = $imageElementProcessor;
        $this->label = $label ?: implode(' ', array_map('ucfirst', explode('_', $code)));
        $this->defaultElement = $imageElementsFactory->create(
            [
                'data' => [
                    ImageElementsInterface::WIDTH => $defaultWidth,
                    ImageElementsInterface::HEIGHT => $defaultHeight,
                    ImageElementsInterface::POS_X => $defaultPosX,
                    ImageElementsInterface::POS_Y => $defaultPosY,
                    ImageElementsInterface::CUSTOM_CSS => $defaultCss
                ]
            ]
        );
        $this->sortOrder = $sortOrder;
    }

    public function getProcessor(): ImageElementProcessorInterface
    {
        return $this->imageElementProcessor;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDefaultElement(): ImageElementsInterface
    {
        return $this->defaultElement;
    }

    public function getDefaultValue(): string
    {
        return $this->imageElementProcessor->getDefaultValue();
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }
}
