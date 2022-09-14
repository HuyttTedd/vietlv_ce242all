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

class From implements ImageElementProcessorInterface
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
        if (!$valueSource->getOrderItemId() || !$this->isFromFieldEnabled()) {
            return '';
        }

        $orderItem = $this->orderItemRepository->get($valueSource->getOrderItemId());
        $from = $orderItem->getProductOptions()[GiftCardOptionInterface::SENDER_NAME] ?? '';

        if ($from) {
            return '<span style="' . $this->imageElementCssMerger->merge($imageElement) . '">'
                . __('From: %1', $from)
                . '</span>';
        }

        return '';
    }

    public function getDefaultValue(): string
    {
        if ($this->isFromFieldEnabled()) {
            return __('From: Jane')->render();
        }

        return '';
    }

    private function isFromFieldEnabled(): bool
    {
        return in_array(GiftCardOptionInterface::SENDER_NAME, $this->configProvider->getGiftCardFields());
    }
}
