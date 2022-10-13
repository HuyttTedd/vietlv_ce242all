<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_OrderAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\OrderAttributes\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Model\ResourceModel\Bookmark\Collection;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\OrderFactory;
use Mageplaza\OrderAttributes\Model\QuoteFactory;

/**
 * Class OrderAttributeDelete
 * @package Mageplaza\OrderAttributes\Observer
 */
class OrderAttributeDelete implements ObserverInterface
{
    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var Collection
     */
    protected $uiBookmarkCollection;

    /**
     * OrderAttributeDelete constructor.
     *
     * @param QuoteFactory $quoteFactory
     * @param OrderFactory $orderFactory
     * @param Collection $uiBookmarkCollection
     */
    public function __construct(
        QuoteFactory $quoteFactory,
        OrderFactory $orderFactory,
        Collection $uiBookmarkCollection
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->orderFactory = $orderFactory;
        $this->uiBookmarkCollection = $uiBookmarkCollection;
    }

    /**
     * @param Observer $observer
     *
     * @return $this|void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof Attribute && !$attribute->isObjectNew()) {
            $this->quoteFactory->create()->deleteAttribute($attribute);
            $this->orderFactory->create()->deleteAttribute($attribute);

            if ($attribute->getIsUsedInGrid()) {
                $this->uiBookmarkCollection->addFieldToFilter('namespace', 'sales_order_grid');
                $this->uiBookmarkCollection->walk('delete');
            }
        }

        return $this;
    }
}
