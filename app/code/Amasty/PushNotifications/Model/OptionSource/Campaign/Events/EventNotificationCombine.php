<?php
declare(strict_types=1);

namespace Amasty\PushNotifications\Model\OptionSource\Campaign\Events;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\LocalizedException;

class EventNotificationCombine implements OptionSourceInterface
{
    const EVENT_NAME = 'amasty_pushnotifications_prepare_campaign_events';

    /**
     * @var array
     */
    private $optionGroups;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    public function __construct(
        array $optionGroups,
        EventManagerInterface $eventManager
    ) {
        $this->optionGroups = $optionGroups;
        $this->eventManager = $eventManager;
    }

    public function toOptionArray(): array
    {
        if (empty($this->optionGroups)) {
            return [];
        }

        $this->eventManager->dispatch(self::EVENT_NAME, ['optionGroups' => &$this->optionGroups]);

        $result = [];

        foreach ($this->optionGroups as $optionGroup) {
            if (empty($optionGroup['optionSources'])) {
                continue;
            }

            if (empty($optionGroup['name'])) {
                throw new LocalizedException(__('Option Group has empty name'));
            }

            $group = [];
            foreach ($optionGroup['optionSources'] as $optionSourceCode => $optionSource) {
                if (!is_subclass_of($optionSource, OptionSourceInterface::class)) {
                    throw new LocalizedException(
                        __('Option Source with code %1 not implements OptionSourceInterface', $optionSourceCode)
                    );
                }
                $group[] = $optionSource->toOptionArray();
            }
            //phpcs:ignore
            $result[] = ['label' => __($optionGroup['name']), 'value' => array_merge([], ...$group)];
        }

        return $result;
    }
}
