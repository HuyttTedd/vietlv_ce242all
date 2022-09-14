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
use Magento\Framework\Stdlib\DateTime\DateTime;

class ExpiryDate implements ImageElementProcessorInterface
{
    /**
     * @var ImageElementCssMerger
     */
    private $imageElementCssMerger;

    /**
     * @var DateTime
     */
    private $dateTime;

    public function __construct(
        ImageElementCssMerger $imageElementCssMerger,
        DateTime $dateTime
    ) {
        $this->imageElementCssMerger = $imageElementCssMerger;
        $this->dateTime = $dateTime;
    }

    public function generateHtml(ImageElementsInterface $imageElement): string
    {
        /** @var GiftCardAccountInterface $valueSource */
        $valueSource = $imageElement->getValueDataSource();
        if (!$valueSource->getExpiredDate()) {
            return '';
        }

        $formattedDate = $this->dateTime->date('d F Y', strtotime($valueSource->getExpiredDate()));
        $value = __('Expiry Date: %1', $formattedDate)->render();

        return '<span style="' . $this->imageElementCssMerger->merge($imageElement) . '">'
            . $value
            . '</span>';
    }

    public function getDefaultValue(): string
    {
        return __('Expiry Date: 01 January 2021')->render();
    }
}
