<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Pdf\ImageToPdf;

interface ImageToPdfAdapterInterface
{
    /**
     * @param string $imageHtml
     * @return string
     */
    public function render(string $imageHtml): string;
}
