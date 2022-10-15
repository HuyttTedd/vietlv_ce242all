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

namespace Mageplaza\OrderAttributes\Plugin\Api;

use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Api\OrderAttributes;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\CollectionFactory;

/**
 * Class OrderGet
 * @package Mageplaza\OrderAttributes\Plugin\Api
 */
class OrderGet
{
    /**
     * @var CollectionFactory
     */
    protected $orderAttributeCollection;

    /**
     * @var OrderExtensionFactory
     */
    protected $orderExtensionFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var null
     */
    protected $attributesRegistry;

    /**
     * Escaper
     *
     * @var Escaper
     */
    protected $escaper;

    /**
     * OrderGet constructor.
     *
     * @param CollectionFactory $orderAttributeCollection
     * @param OrderExtensionFactory $orderExtensionFactory
     * @param Data $helperData
     * @param Escaper $escaper
     */
    public function __construct(
        CollectionFactory $orderAttributeCollection,
        OrderExtensionFactory $orderExtensionFactory,
        Data $helperData,
        Escaper $escaper
    ) {
        $this->orderAttributeCollection = $orderAttributeCollection;
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->helperData = $helperData;
        $this->escaper = $escaper;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $resultOrder
     *
     * @return OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface $resultOrder
    ) {
        $this->addOrderAttributes($resultOrder);

        return $resultOrder;
    }

    /**
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    protected function addOrderAttributes(OrderInterface $order)
    {
        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes && $extensionAttributes->getMpOrderAttributes()) {
            return $order;
        }

        $orderData = [];
        if (!$this->attributesRegistry) {
            $this->attributesRegistry = $this->orderAttributeCollection->create();
        }

        /** @var Attribute $attribute */
        foreach ($this->attributesRegistry->getItems() as $attribute) {
            $value = [];
            $attrCode = $attribute->getAttributeCode();
            $frontendInput = $attribute->getFrontendInput();

            switch ($frontendInput) {
                case 'boolean':
                case 'select':
                case 'multiselect':
                case 'select_visual':
                case 'multiselect_visual':
                    $value = $order->getData($attrCode . '_option');
                    break;
                case 'image':
                case 'file':
                    $value[] = $order->getData($attrCode . '_name');
                    $value[] = $order->getData($attrCode . '_url');

                    break;
                case 'textarea_visual':
                    $value = $this->minifyHtml($order->getData($attrCode));
                    break;
                default:
                    $value = $order->getData($attrCode);
            }

            if ($value) {
                $orderData[] = new OrderAttributes(
                    [
                        OrderAttributes::VALUE => $value,
                        OrderAttributes::LABEL => $order->getData($attrCode . '_label'),
                        OrderAttributes::ATTRIBUTE_CODE => $attribute->getAttributeCode(),
                        OrderAttributes::SHOW_IN_FRONTEND_ORDER => $attribute->getShowInFrontendOrder()
                    ]
                );
            }
        }

        $extensionAttributes->setMpOrderAttributes($orderData);
        $order->setExtensionAttributes($extensionAttributes);

        return $order;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param Collection $resultOrder
     *
     * @return Collection
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        Collection $resultOrder
    ) {
        /** @var  $order */
        foreach ($resultOrder->getItems() as $order) {
            $this->helperData->addDataToOrder($order);
            $this->afterGet($subject, $order);
        }

        return $resultOrder;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    public function minifyHtml($content)
    {
        $search = [
            '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
            '/[^\S ]+\</s',     // strip whitespaces before tags, except space
            '/(\s)+/s',         // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/', // Remove HTML comments
            '/(")/', // Remove HTML comments
        ];

        $replace = ['>', '<', '\\1', '', "'"];

        return preg_replace($search, $replace, $content);
    }
}
