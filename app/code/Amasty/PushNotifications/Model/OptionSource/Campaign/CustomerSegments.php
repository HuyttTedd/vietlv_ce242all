<?php

namespace Amasty\PushNotifications\Model\OptionSource\Campaign;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class CustomerSegments
 */
class CustomerSegments implements OptionSourceInterface
{
    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager,
        Manager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManager;
    }

    public function toOptionArray()
    {
        $result = [];

        if ($this->moduleManager->isOutputEnabled('Amasty_Segments')) {
            $segmentCollection = $this->objectManager
                ->create(\Amasty\Segments\Model\ResourceModel\Segment\Collection::class)
                ->addActiveFilter();

            foreach ($segmentCollection->getItems() as $item) {
                $result[] = [
                    'value' => $item->getSegmentId(),
                    'label' => $item->getName()
                ];
            }
        }

        return $result;
    }
}
