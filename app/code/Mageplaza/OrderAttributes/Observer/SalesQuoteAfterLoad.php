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
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\QuoteFactory;

/**
 * Class SalesQuoteAfterLoad
 * @package Mageplaza\OrderAttributes\Observer
 */
class SalesQuoteAfterLoad implements ObserverInterface
{
    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var Data
     */
    protected $data;

    /**
     * @param QuoteFactory $quoteFactory
     * @param Data $data
     */
    public function __construct(QuoteFactory $quoteFactory, Data $data)
    {
        $this->quoteFactory = $quoteFactory;
        $this->data = $data;
    }

    /**
     * @param Observer $observer
     *
     * @return $this|void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if ($this->data->isEnabled($quote->getStoreId())) {
            $quoteAttributeModel = $this->quoteFactory->create();
            $quoteAttributeModel->load($quote->getId());
            if ($quoteAttributeModel->getId()) {
                $result = $this->data->prepareAttributes($quote->getStoreId(), $quoteAttributeModel->getData());
                $quote->addData($result);
            }
        }

        return $this;
    }
}
