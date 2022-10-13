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

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Sales\Api\Data\OrderInterface;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\Order;
use Mageplaza\OrderAttributes\Model\OrderFactory;
use Mageplaza\OrderAttributes\Model\QuoteFactory;
use Psr\Log\LoggerInterface;

/**
 * Class QuoteSubmitSuccess
 * @package Mageplaza\OrderAttributes\Observer
 */
class QuoteSubmitSuccess implements ObserverInterface
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
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * QuoteSubmitSuccess constructor.
     *
     * @param QuoteFactory $quoteFactory
     * @param OrderFactory $orderFactory
     * @param LoggerInterface $logger
     * @param Data $helperData
     */
    public function __construct(
        QuoteFactory $quoteFactory,
        OrderFactory $orderFactory,
        LoggerInterface $logger,
        Data $helperData
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->orderFactory = $orderFactory;
        $this->logger = $logger;
        $this->helperData = $helperData;
    }

    /**
     * @param Observer $observer
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        /** @var AbstractExtensibleModel|OrderInterface $order */
        $order = $observer->getEvent()->getOrder();
        $storeId = $order->getStoreId();
        if ($this->helperData->isEnabled($storeId)) {
            $quoteModel = $this->quoteFactory->create()->load($order->getQuoteId());

            /**
             * @var Order $orderModel
             */
            $orderModel = $this->orderFactory->create()->load($order->getId());

            if (!$orderModel->getData() && $data = $quoteModel->getData()) {
                $attributes = $this->helperData->getOrderAttributesCollection(
                    $storeId,
                    $order->getCustomerGroupId(),
                    false,
                    [
                        'frontend_input' => ['in' => ['image', 'file']]
                    ]
                );
                /** @var Attribute $attribute */
                foreach ($attributes as $attribute) {
                    if (!$this->helperData->isVisible($attribute, $storeId, $order->getCustomerGroupId())) {
                        continue;
                    }

                    $attrCode = $attribute->getAttributeCode();
                    if (!empty($data[$attrCode])) {
                        $file = $this->helperData->jsonDecodeData($data[$attrCode]);
                        if (!empty($file['file'])) {
                            try {
                                $result = $this->helperData->moveTemporaryFile($file);

                                $data[$attrCode] = $result;
                                $order->setData($attrCode . '_name', $this->helperData->prepareFileName($result));
                                $order->setData(
                                    $attrCode . '_url',
                                    $this->helperData->prepareFileValue($attribute->getFrontendInput(), $result)
                                );
                            } catch (Exception $e) {
                                $this->logger->critical($e);
                            }
                        }
                    }
                }

                $orderModel->saveAttributeData($order->getId(), $data);
            }
        }
    }
}
