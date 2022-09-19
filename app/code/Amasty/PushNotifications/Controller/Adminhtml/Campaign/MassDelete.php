<?php

namespace Amasty\PushNotifications\Controller\Adminhtml\Campaign;

class MassDelete extends \Amasty\PushNotifications\Controller\Adminhtml\AbstractMassAction
{
    /**
     * @param $item
     */
    protected function itemAction($item)
    {
        $this->repository->deleteById($item->getCampaignId());
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t delete item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been deleted.', $collectionSize);
        }

        return __('No records have been deleted.');
    }
}
