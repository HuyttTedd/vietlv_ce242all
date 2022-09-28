<?php

namespace Amasty\PushNotifications\Block\Adminhtml\Dashboard\Campaigns\Grid\Renderer;

use Amasty\PushNotifications\Model\OptionSource\Campaign\Status;

class Clicks extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render action
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getStatus() == Status::STATUS_PASSED) {
            $clickedPercent = $row->getShownCounter() !== 0
                ? ($row->getClickedCounter() / $row->getShownCounter()) * 100
                : 0;

            $resultRow = __('%1 (%2%)', $row->getClickedCounter(), number_format($clickedPercent, 2));
        } else {
            $resultRow = __('Scheduled');
        }

        return $resultRow;
    }
}
