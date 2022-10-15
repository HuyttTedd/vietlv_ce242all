<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-banner
 * @version   1.0.11
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Banner\Controller\Adminhtml\Placeholder;

use Mirasvit\Banner\Api\Data\PlaceholderInterface;
use Mirasvit\Banner\Controller\Adminhtml\AbstractPlaceholder;

class Delete extends AbstractPlaceholder
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $model = $this->initModel();

        $resultRedirect = $this->resultRedirectFactory->create();

        if ($model->getId()) {
            try {
                $this->placeholderRepository->delete($model);

                $this->messageManager->addSuccessMessage(__('The placeholder has been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', [PlaceholderInterface::ID => $model->getId()]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('This placeholder no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }
    }
}
