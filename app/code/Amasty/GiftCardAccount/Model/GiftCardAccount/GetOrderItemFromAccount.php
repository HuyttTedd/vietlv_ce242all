<?php
declare(strict_types=1);

namespace Amasty\GiftCardAccount\Model\GiftCardAccount;

use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Model\Order\ResourceModel\Order as OrderResource;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\OrderItemInterfaceFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\ProductOption;
use Magento\Sales\Model\ResourceModel\Order\Item as OrderItemResource;

class GetOrderItemFromAccount
{
    /**
     * @var OrderItemInterfaceFactory
     */
    private $orderItemFactory;

    /**
     * @var OrderItemResource
     */
    private $orderItemResource;

    /**
     * @var ProductOption
     */
    private $productOption;

    /**
     * @var OrderResource
     */
    private $orderResource;

    /**
     * @var OrderInterfaceFactory
     */
    private $orderFactory;

    /**
     * Storage for loaded order items
     *
     * @var array
     */
    private $processedOrderItems = [];

    public function __construct(
        OrderItemInterfaceFactory $orderItemFactory,
        OrderItemResource $orderItemResource,
        ProductOption $productOption,
        OrderResource $orderResource,
        OrderInterfaceFactory $orderFactory
    ) {
        $this->orderItemFactory = $orderItemFactory;
        $this->orderItemResource = $orderItemResource;
        $this->productOption = $productOption;
        $this->orderResource = $orderResource;
        $this->orderFactory = $orderFactory;
    }

    /**
     * Retrieve order item from account if it exists
     *
     * @param GiftCardAccountInterface|Account $account
     * @return OrderItemInterface|null
     */
    public function execute(GiftCardAccountInterface $account): ?OrderItemInterface
    {
        if (!($itemId = $account->getOrderItemId())) {
            return null;
        }

        if (!isset($this->processedOrderItems[$itemId])) {
            $orderItem = $this->loadOrderItem($itemId);

            if ($orderItem && $orderId = $orderItem->getOrderId()) {
                /** @var OrderInterface|Order $order */
                $order = $this->orderFactory->create();
                $this->orderResource->load($order, $orderId);
                $orderItem->setOrder($order);
            }

            $this->processedOrderItems[$itemId] = $orderItem;
        }

        return $this->processedOrderItems[$itemId];
    }

    /**
     * @param int $itemId
     * @return OrderItemInterface|null
     */
    private function loadOrderItem(int $itemId): ?OrderItemInterface
    {
        /** @var OrderItemInterface|Item $orderItem */
        $orderItem = $this->orderItemFactory->create();
        $this->orderItemResource->load($orderItem, $itemId);
        if (!$orderItem->getItemId()) {
            return null;
        }
        $this->productOption->add($orderItem);

        return $orderItem;
    }
}
