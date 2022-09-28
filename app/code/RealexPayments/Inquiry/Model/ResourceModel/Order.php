<?php

declare(strict_types=1);

namespace RealexPayments\Inquiry\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;

/**
 * Class Order.
 */
class Order
{
    const PAYMENT_TABLE = 'sales_order_payment';
    const ORDER_TABLE = 'sales_order';
    const REALEX_PAYMENTS_METHOD_CODE = 'realexpayments_hpp';
    const STATUS_PENDING = 'pending';
    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * @return array
     */
    public function getOrderIdsForInquiry(): array
    {
        $connection = $this->resource->getConnection();
        $query = $connection
            ->select()
            ->from(
                ['i' => self::ORDER_TABLE],
                ['entity_id']
            )
            ->where(
                'status = ?',
                self::STATUS_PENDING
            )
            ->order('entity_id DESC')
            ->join(
                ['e' => self::PAYMENT_TABLE],
                'i.entity_id = e.parent_id',
                []
            )
            ->where(
                'e.method = ?',
                self::REALEX_PAYMENTS_METHOD_CODE
            )
            ->limit(50);

        return $connection->fetchCol($query);
    }
}
