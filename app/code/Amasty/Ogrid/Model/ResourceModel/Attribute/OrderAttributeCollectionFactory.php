<?php

namespace Amasty\Ogrid\Model\ResourceModel\Attribute;

class OrderAttributeCollectionFactory
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }
    public function create()
    {
        return $this->objectManager->create('Amasty\Orderattr\Model\ResourceModel\Order\Attribute\Collection');
    }
}