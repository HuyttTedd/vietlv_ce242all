<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Image\ImageElementProcessors;

use Amasty\GiftCard\Api\Data\GiftCardOptionInterface;
use Amasty\GiftCard\Api\Data\ImageElementsInterface;
use Amasty\GiftCard\Model\ConfigProvider;
use Amasty\GiftCard\Model\Image\Utils\ImageElementCssMerger;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;

class Message implements ImageElementProcessorInterface
{
    /**
     * @var ImageElementCssMerger
     */
    private $imageElementCssMerger;

    /**
     * @var OrderItemRepositoryInterface
     */
    private $orderItemRepository;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ImageElementCssMerger $imageElementCssMerger,
        ConfigProvider $configProvider,
        OrderItemRepositoryInterface $orderItemRepository
    ) {
        $this->imageElementCssMerger = $imageElementCssMerger;
        $this->orderItemRepository = $orderItemRepository;
        $this->configProvider = $configProvider;
    }

    public function generateHtml(ImageElementsInterface $imageElement): string
    {
        /** @var GiftCardAccountInterface $valueSource */
        $valueSource = $imageElement->getValueDataSource();
        if (!$valueSource->getOrderItemId() || !$this->isMessageFieldEnabled()) {
            return '';
        }

        $orderItem = $this->orderItemRepository->get($valueSource->getOrderItemId());
        $message = $orderItem->getProductOptions()[GiftCardOptionInterface::MESSAGE] ?? '';

        if ($message) {
            return '<span style="' . $this->imageElementCssMerger->merge($imageElement) . '">'
                . $message
                . '</span>';
        }

        return '';
    }

    public function getDefaultValue(): string
    {
        if ($this->isMessageFieldEnabled()) {
            return __('Hope this brightens your day!')->render();
        }

        return '';
    }

    private function isMessageFieldEnabled(): bool
    {
        return in_array(GiftCardOptionInterface::MESSAGE, $this->configProvider->getGiftCardFields());
    }
}
