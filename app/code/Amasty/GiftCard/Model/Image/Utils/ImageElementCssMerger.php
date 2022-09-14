<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Image\Utils;

use Amasty\GiftCard\Api\Data\ImageElementsInterface;

class ImageElementCssMerger
{
    public const KEY_PART = 0;
    public const VALUE_PART = 1;

    public function merge(ImageElementsInterface $imageElement, array $additionalStyles = []): string
    {
        $resultCss = '';
        $customCssArray = $this->getCustomCssArray($imageElement);
        $replacements = array_merge(
            $additionalStyles,
            [
                'position' => 'absolute',
                'overflow' => 'hidden',
                'padding' => '15px',
                'height' => $imageElement->getHeight() . 'px',
                'width' => $imageElement->getWidth() . 'px',
                'left' => $imageElement->getPosX() . 'px',
                'top' => $imageElement->getPosY() . 'px'
            ]
        );

        foreach ($replacements as $key => $replacement) {
            if (array_key_exists($key, $customCssArray)) {
                unset($customCssArray[$key]);
            }
            $resultCss .= "$key:{$replacement};";
        }
        foreach ($customCssArray as $key => $value) {
            $resultCss .= "$key:{$value};";
        }

        $replaceables = [
            'text-align' => 'left'
        ];

        foreach ($replaceables as $key => $replaceable) {
            $resultCss = "$key:{$replaceable};" . $resultCss;
        }

        return $resultCss;
    }

    private function getCustomCssArray(ImageElementsInterface $imageElement): array
    {
        if ($imageElement->getCustomCss()) {
            $customCss = rtrim((string)$imageElement->getCustomCss(), ';') . ';';
        } else {
            return [];
        }

        $result = [];
        $styles = array_filter(explode(';', $customCss));
        foreach ($styles as $style) {
            $styleParts = array_map('trim', explode(':', $style));

            if (isset($styleParts[self::VALUE_PART])) {
                $result[$styleParts[self::KEY_PART]] = $styleParts[self::VALUE_PART];
            }
        }

        return $result;
    }
}
