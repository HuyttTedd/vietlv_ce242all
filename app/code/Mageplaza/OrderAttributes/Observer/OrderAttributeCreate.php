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
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\OrderFactory;
use Mageplaza\OrderAttributes\Model\QuoteFactory;

/**
 * Class OrderAttributeCreate
 * @package Mageplaza\OrderAttributes\Observer
 */
class OrderAttributeCreate implements ObserverInterface
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
     * @param QuoteFactory $quoteFactory
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        QuoteFactory $quoteFactory,
        OrderFactory $orderFactory
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->orderFactory = $orderFactory;
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
        if ($attribute instanceof Attribute && $attribute->isObjectNew()) {
            $this->quoteFactory->create()->createAttribute($attribute);
            $this->orderFactory->create()->createAttribute($attribute);
        }

        return $this;
    }
}
