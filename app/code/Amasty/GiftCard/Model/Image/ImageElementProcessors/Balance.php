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
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;

class Balance implements ImageElementProcessorInterface
{
    /**
     * @var ImageElementCssMerger
     */
    private $imageElementCssMerger;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CurrencyInterface
     */
    private $localeCurrency;

    public function __construct(
        ImageElementCssMerger $imageElementCssMerger,
        StoreManagerInterface $storeManager,
        CurrencyInterface $localeCurrency
    ) {
        $this->imageElementCssMerger = $imageElementCssMerger;
        $this->storeManager = $storeManager;
        $this->localeCurrency = $localeCurrency;
    }

    public function generateHtml(ImageElementsInterface $imageElement): string
    {
        /** @var GiftCardAccountInterface $valueSource */
        $valueSource = $imageElement->getValueDataSource();
        $currency = $this->localeCurrency->getCurrency(
            $this->storeManager->getWebsite($valueSource->getWebsiteId())->getBaseCurrencyCode()
        );
        $value = $currency->toCurrency(sprintf("%f", $valueSource->getInitialValue()));

        return '<span style="' . $this->imageElementCssMerger->merge($imageElement) . '">'
            . $value
            . '</span>';
    }

    public function getDefaultValue(): string
    {
        return __('$100.00')->render();
    }
}
