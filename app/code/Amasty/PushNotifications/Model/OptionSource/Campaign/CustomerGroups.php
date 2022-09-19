<?php

namespace Amasty\PushNotifications\Model\OptionSource\Campaign;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Model\ResourceModel\Group\Collection;

/**
 * Class CustomerGroups
 */
class CustomerGroups implements OptionSourceInterface
{
    /**
     * @var Collection
     */
    private $customerGroupCollection;

    public function __construct(
        Collection $customerGroupCollection
    ) {
        $this->customerGroupCollection = $customerGroupCollection;
    }

    public function toOptionArray()
    {
        return $this->customerGroupCollection->toOptionArray();
    }
}
