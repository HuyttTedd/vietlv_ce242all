<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Pdf;

use Amasty\GiftCard\Model\Pdf\ImageToPdf\ImageToPdfAdapterInterface;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;

class PdfImageConverter
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ImageToPdf\ImageToPdfAdapterInterface
     */
    private $imageToPdfAdapter;

    /**
     * @var array
     */
    private $generationCache = [];

    public function __construct(
        LoggerInterface $logger,
        ImageToPdfAdapterInterface $imageToPdfAdapter
    ) {
        $this->logger = $logger;
        $this->imageToPdfAdapter = $imageToPdfAdapter;
    }

    public function convert(string $imageHtml, string $code = ''): string
    {
        if (!empty($code) && isset($this->generationCache[$code])) {
            return $this->generationCache[$code];
        }

        try {
            $pdfString = $this->createPdfPageFromImageString($imageHtml);
        } catch (\Exception $e) {
            $pdfString = '';
            $this->logger->critical($e->getMessage());
        }

        if (!empty($code)) {
            $this->generationCache[$code] = $pdfString;
        }

        return $pdfString;
    }

    private function createPdfPageFromImageString(string $imageHtml): string
    {
        return $this->imageToPdfAdapter->render($imageHtml);
    }
}
