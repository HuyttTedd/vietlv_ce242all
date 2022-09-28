<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Image\ImageElementProcessors;

use Amasty\GiftCard\Api\Data\ImageElementsInterface;

interface ImageElementProcessorInterface
{
    public function generateHtml(ImageElementsInterface $imageElement): string;

    public function getDefaultValue(): string;
}
