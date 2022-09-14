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

class To implements ImageElementProcessorInterface
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
        if (!$valueSource->getOrderItemId() || !$this->isToFieldEnabled()) {
            return '';
        }

        $orderItem = $this->orderItemRepository->get($valueSource->getOrderItemId());
        $to = $orderItem->getProductOptions()[GiftCardOptionInterface::RECIPIENT_NAME] ?? '';

        if ($to) {
            return '<span style="' . $this->imageElementCssMerger->merge($imageElement) . '">'
                . __('To: %1', $to)
                . '</span>';
        }

        return '';
    }

    public function getDefaultValue(): string
    {
        if ($this->isToFieldEnabled()) {
            return __('To: David')->render();
        }

        return '';
    }

    private function isToFieldEnabled(): bool
    {
        return in_array(GiftCardOptionInterface::RECIPIENT_NAME, $this->configProvider->getGiftCardFields());
    }
}
