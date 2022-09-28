<?php

namespace Amasty\PushNotifications\Block\Adminhtml\Dashboard\Campaigns\Grid\Renderer;

use Amasty\PushNotifications\Model\Builder\DateTimeBuilder;
use Amasty\PushNotifications\Model\OptionSource\Campaign\Status;
use Magento\Backend\Block\Context;

class ScheduledAt extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var DateTimeBuilder
     */
    private $dateTimeBuilder;

    public function __construct(
        Context $context,
        DateTimeBuilder $dateTimeBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dateTimeBuilder = $dateTimeBuilder;
    }

    /**
     * Render action
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        return $this->dateTimeBuilder->getScheduledDateFromDifference($row);
    }
}
