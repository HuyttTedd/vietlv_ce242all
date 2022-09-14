<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Image\ImageElementProcessors;

use Amasty\GiftCard\Api\Data\ImageElementsInterface;
use Amasty\GiftCard\Model\Image\Utils\ImageElementCssMerger;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

class Code implements ImageElementProcessorInterface
{
    /**
     * @var ImageElementCssMerger
     */
    private $imageElementCssMerger;

    public function __construct(
        ImageElementCssMerger $imageElementCssMerger
    ) {
        $this->imageElementCssMerger = $imageElementCssMerger;
    }

    public function generateHtml(ImageElementsInterface $imageElement): string
    {
        /** @var GiftCardAccountInterface $valueSource */
        $valueSource = $imageElement->getValueDataSource();

        return '<span style="' . $this->imageElementCssMerger->merge($imageElement) . '">'
            . $valueSource->getCodeModel()->getCode()
            . '</span>';
    }

    public function getDefaultValue(): string
    {
        return __('GIFT_XXX_XXX')->render();
    }
}
