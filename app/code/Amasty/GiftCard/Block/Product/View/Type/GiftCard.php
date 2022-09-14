<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Block\Product\View\Type;

use Amasty\GiftCard\Api\Data\GiftCardOptionInterface;
use Amasty\GiftCard\Api\Data\ImageInterface;
use Amasty\GiftCard\Model\Image\Repository;
use Amasty\GiftCard\Model\ConfigProvider;
use Amasty\GiftCard\Model\Image\ResourceModel\CollectionFactory;
use Amasty\GiftCard\Model\OptionSource\GiftCardOption;
use Amasty\GiftCard\Utils\FileUpload;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\ArrayUtils;

class GiftCard extends \Magento\Catalog\Block\Product\View\AbstractView
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Repository
     */
    private $imageRepository;

    /**
     * @var GiftCardOption
     */
    private $giftCardOption;

    /**
     * @var ListsInterface
     */
    private $localeLists;

    /**
     * @var CollectionFactory
     */
    protected $imageCollectionFactory;

    /**
     * @var FileUpload
     */
    protected $fileUpload;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonSerializer;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var array
     */
    private $amounts = [];

    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        ConfigProvider $configProvider,
        Repository $imageRepository,
        GiftCardOption $giftCardOption,
        ListsInterface $localeLists,
        CollectionFactory $imageCollectionFactory,
        FileUpload $fileUpload,
        Json $jsonSerializer,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($context, $arrayUtils, $data);
        $this->configProvider = $configProvider;
        $this->imageRepository = $imageRepository;
        $this->giftCardOption = $giftCardOption;
        $this->localeLists = $localeLists;
        $this->imageCollectionFactory = $imageCollectionFactory;
        $this->fileUpload = $fileUpload;
        $this->jsonSerializer = $jsonSerializer;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param Product $product
     *
     * @return bool
     */
    public function isConfigured(Product $product): bool
    {
        if (!$product->getAmAllowOpenAmount() && !$product->getAmGiftcardPrices()) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getImages(): string
    {
        $images = [];

        if ($productImagesId = $this->getProduct()->getAmGiftcardCodeImage()) {
            $productImagesId = explode(',', $productImagesId);
            $collection = $this->imageCollectionFactory->create()
                ->addFieldToFilter(ImageInterface::IMAGE_ID, ['in' => $productImagesId]);

            foreach ($collection->getItems() as $image) {
                try {
                    $images[] = [
                        'id' => $image->getImageId(),
                        'src' => $this->fileUpload->getImageUrl(
                            $image->getImagePath()
                        )
                    ];
                } catch (LocalizedException $e) {
                    null;
                }
            }
        }

        return $this->jsonSerializer->serialize($images);
    }

    /**
     * @return string
     */
    public function getAvailableOptions(): string
    {
        $options = [];
        $allFields = $this->giftCardOption->toArray();
        $fields = $this->configProvider->getGiftCardFields();

        foreach ($allFields as $value => $label) {
            if (in_array($value, $fields)) {
                $options[] = $value;
            }
        }

        return $this->jsonSerializer->serialize($options);
    }

    /**
     * @return string
     */
    public function getListTimezones(): string
    {
        $result = [];
        $allTimeZones = $this->localeLists->getOptionTimezones();
        $selectedTimeZones = $this->configProvider->getGiftCardTimezone();

        if (!$selectedTimeZones) {
            return $this->jsonSerializer->serialize($allTimeZones);
        }

        foreach ($allTimeZones as $timeZone) {
            if (in_array($timeZone['value'], $selectedTimeZones)) {
                $result[] = [
                    'value' => $timeZone['value'],
                    'label' => $timeZone['label']
                ];
            }
        }

        return $this->jsonSerializer->serialize($result);
    }

    /**
     * @return bool
     */
    public function getAllowUsersUploadImages(): bool
    {
        return $this->configProvider->isAllowUserImages();
    }

    /**
     * @return string
     */
    public function getTooltipContent(): string
    {
        return $this->configProvider->getImageUploadTooltip();
    }

    /**
     * @return string
     */
    public function getDefaultValues(): string
    {
        return $this->jsonSerializer->serialize($this->getProduct()->getPreconfiguredValues()->getData());
    }

    /**
     * @return string
     */
    public function getCustomImageUrl(): string
    {
        if ($img = $this->getProduct()->getPreconfiguredValues()->getData(GiftCardOptionInterface::CUSTOM_IMAGE)) {
            return $this->fileUpload->getTempImgUrl($img);
        }

        return '';
    }

    /**
     * @return bool
     */
    public function isShowPrices(): bool
    {
        return $this->getProduct()->getAmAllowOpenAmount()
            || count((array)$this->getProduct()->getAmGiftcardPrices()) > 1;
    }

    /**
     * @return bool
     */
    public function isSinglePrice(): bool
    {
        $amountsCount = count((array)$this->getProduct()->getAmGiftcardPrices());
        $haveCustomAmount = $this->getProduct()->getAmAllowOpenAmount();

        return ($amountsCount === 1) && !$haveCustomAmount;
    }

    /**
     * @return string
     */
    public function getPredefinedAmountsJson(): string
    {
        if (!empty($this->amounts)) {
            return $this->jsonSerializer->serialize($this->amounts);
        }

        foreach ((array)$this->getProduct()->getAmGiftcardPrices() as $amount) {
            $this->amounts[] = [
                'value' => (float)$amount['value'],
                'convertValue' => $this->priceCurrency->convert((float)$amount['value']),
                'price' => $this->convertAndFormatCurrency((float)$amount['value'], false)
            ];
        }

        return $this->jsonSerializer->serialize($this->amounts);
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        $currency = $this->priceCurrency->getCurrency();

        return $currency->getCurrencyCode() ?: $currency->getCurrencySymbol();
    }

    /**
     * @return float
     */
    public function getFeeValueConverted(): float
    {
        return $this->priceCurrency->convert($this->getProduct()->getAmGiftcardFeeValue());
    }

    /**
     * @param float $amount
     * @param bool $includeContainer
     * @return string
     */
    public function convertAndFormatCurrency(float $amount, bool $includeContainer = true): string
    {
        return $this->priceCurrency->convertAndFormat($amount, $includeContainer);
    }

    /**
     * @param float $amount
     * @return float
     */
    public function convertAndRoundCurrency(float $amount): float
    {
        return $this->priceCurrency->convertAndRound($amount);
    }
}
