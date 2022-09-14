<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Image\OutputBuilders;

use Amasty\GiftCard\Api\Data\ImageElementsInterface;

interface OutputBuilderInterface
{
    /**
     * @param ImageElementsInterface[] $imageElements
     * @return string
     */
    public function build(array $imageElements): string;
}
