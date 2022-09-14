<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Image\OutputBuilders;

use Amasty\Base\Model\Serializer;
use Amasty\GiftCard\Api\Data\ImageElementsInterface;
use Amasty\GiftCard\Model\Image\ImageElementConfigProvider;

class UiOutputBuilder implements OutputBuilderInterface
{
    public const DEFAULT_KEY = 'default';
    public const VALUE_KEY = 'value';
    public const LABEL_KEY = 'label';

    /**
     * @var ImageElementConfigProvider
     */
    private $imageElementConfigProvider;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        ImageElementConfigProvider $imageElementConfigProvider,
        Serializer $serializer
    ) {
        $this->imageElementConfigProvider = $imageElementConfigProvider;
        $this->serializer = $serializer;
    }

    public function build(array $imageElements): string
    {
        $arrayOutput = [];
        $imageElementsByKeys = $this->convertToKeyByName($imageElements);
        foreach ($this->imageElementConfigProvider->getAll() as $imageElementConfig) {
            if (!($defaultValue = $imageElementConfig->getDefaultValue())) {
                continue;
            }
            $imageElement = $imageElementsByKeys[$imageElementConfig->getCode()] ?? null;
            $defaultElement = $imageElementConfig->getDefaultElement();
            $arrayOutput[] = [
                ImageElementsInterface::NAME => $imageElementConfig->getCode(),
                ImageElementsInterface::WIDTH => $imageElement ? $imageElement->getWidth() : null,
                ImageElementsInterface::HEIGHT => $imageElement ? $imageElement->getHeight() : null,
                ImageElementsInterface::POS_X => $imageElement ? $imageElement->getPosX() : null,
                ImageElementsInterface::POS_Y => $imageElement ? $imageElement->getPosY() : null,
                ImageElementsInterface::CUSTOM_CSS => $imageElement ? $imageElement->getCustomCss() : null,
                self::DEFAULT_KEY => [
                    ImageElementsInterface::WIDTH => $defaultElement->getWidth(),
                    ImageElementsInterface::HEIGHT => $defaultElement->getHeight(),
                    ImageElementsInterface::POS_X => $defaultElement->getPosX(),
                    ImageElementsInterface::POS_Y => $defaultElement->getPosY(),
                    ImageElementsInterface::CUSTOM_CSS => $defaultElement->getCustomCss(),
                ],
                self::VALUE_KEY => $defaultValue,
                self::LABEL_KEY => $imageElementConfig->getLabel()
            ];
        }

        return $this->serializer->serialize($arrayOutput);
    }

    private function convertToKeyByName(array $imageElements): array
    {
        $result = [];
        foreach ($imageElements as $element) {
            $result[$element->getName()] = $element;
        }

        return $result;
    }
}
