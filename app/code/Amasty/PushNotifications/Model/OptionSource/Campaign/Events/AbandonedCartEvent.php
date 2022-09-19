<?php
declare(strict_types=1);

namespace Amasty\PushNotifications\Model\OptionSource\Campaign\Events;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Module\Manager;

class AbandonedCartEvent implements OptionSourceInterface
{
    const ABANDONED_CART_EVENT = 'amasty_acart_pushnotifications';

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        Manager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [
                'disabled' => !$this->moduleManager->isEnabled('Amasty_Acart'), //field flag used in multiselect.js
                'value' => self::ABANDONED_CART_EVENT,
                'label' => __('Abandoned Shopping Cart')
            ]
        ];
    }

    /**
     * @return array|false
     */
    public function toArray()
    {
        $optionArray = $this->toOptionArray();
        $labels = array_column($optionArray, 'label');
        $values = array_column($optionArray, 'value');

        return array_combine($values, $labels);
    }
}
