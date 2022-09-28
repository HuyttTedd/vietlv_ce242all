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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Logo implements ImageElementProcessorInterface
{
    /**
     * @var ImageElementCssMerger
     */
    private $imageElementCssMerger;

    /**
     * @var Mime
     */
    private $mime;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var File
     */
    private $file;

    public function __construct(
        ImageElementCssMerger $imageElementCssMerger,
        Mime $mime,
        Repository $assetRepo,
        ScopeConfigInterface $scopeConfig,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        File $file
    ) {
        $this->imageElementCssMerger = $imageElementCssMerger;
        $this->mime = $mime;
        $this->scopeConfig = $scopeConfig;
        $this->assetRepo = $assetRepo;
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        $this->file = $file;
    }

    public function generateHtml(ImageElementsInterface $imageElement): string
    {
        /** @var GiftCardAccountInterface $dataSource */
        $dataSource = $imageElement->getValueDataSource();

        if ($logoSrc = $this->getLogoSrc($dataSource->getWebsiteId())) {
            return '<div style="' . $this->imageElementCssMerger->merge($imageElement)
                . '"><img style="width:100%;height:100%;" src="' . $logoSrc . '"/></div>';
        }

        return '';
    }

    public function getDefaultValue(): string
    {
        if ($logoSrc = $this->getLogoSrc()) {
            return $logoSrc;
        }

        return '';
    }

    private function getLogoSrc(?int $websiteId = null): string
    {
        $storeLogoPath = $this->scopeConfig->getValue(
            'design/header/logo_src',
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getWebsite($websiteId)->getDefaultStore()->getId()
        );
        if ($storeLogoPath) {
            $absolutePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath(
                \Magento\Config\Model\Config\Backend\Image\Logo::UPLOAD_DIR . DIRECTORY_SEPARATOR
                . $storeLogoPath
            );
        } else {
            $absolutePath = $this->assetRepo->createAsset('images/logo.svg')->getSourceFile();
        }

        if ($content = $this->file->read($absolutePath)) {
            return 'data:' . $this->mime->getMimeType($absolutePath) . ';base64,' . base64_encode($content);
        }

        return '';
    }
}
