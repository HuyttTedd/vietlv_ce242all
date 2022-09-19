<?php

namespace Amasty\PushNotifications\Controller\Adminhtml\Campaign;

use Amasty\PushNotifications\Model\OptionSource\Campaign\Active;

class MassStatus extends \Amasty\PushNotifications\Controller\Adminhtml\AbstractMassAction
{
    /**
     * @param $item
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    protected function itemAction($item)
    {
        $status = $this->getStatus();

        $item->setIsActive($status);
        $this->repository->save($item);
    }

    /**
     * @return int
     */
    private function getStatus()
    {
        return $this->getRequest()->getParam('status') == 'activate'
            ? Active::STATUS_ACTIVE : Active::STATUS_INACTIVE;
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
        if ($this->getStatus() == Active::STATUS_ACTIVE) {
            return $this->getActivateMessage($collectionSize);
        } else {
            return $this->getDeactivateMessage($collectionSize);
        }
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    private function getActivateMessage($collectionSize)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been activated.', $collectionSize);
        }

        return __('No records have been activated.');
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    private function getDeactivateMessage($collectionSize)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been deactivated.', $collectionSize);
        }

        return __('No records have been deactivated.');
    }
}
