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
use Mirasvit\Core\Service\CompatibilityService;

class Save extends AbstractPlaceholder
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id             = $this->getRequest()->getParam(PlaceholderInterface::ID);
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getParams();


        if ($data) {
            $model = $this->initModel();
            $data  = $this->filterPostData($model, $data);

            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This placeholder no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            foreach ($data as $key => $value) {
                $model->setDataUsingMethod($key, $value);
            }

            try {
                $this->placeholderRepository->save($model);

                $this->messageManager->addSuccessMessage(__('You have saved the banner.'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', [PlaceholderInterface::ID => $model->getId()]);
                }

                return $this->context->getResultRedirectFactory()->create()->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath(
                    '*/*/edit',
                    [PlaceholderInterface::ID => $this->getRequest()->getParam(PlaceholderInterface::ID)]
                );
            }
        } else {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addErrorMessage('No data to save.');

            return $resultRedirect;
        }
    }

    /**
     * @param PlaceholderInterface $model
     * @param array                $rawData
     *
     * @return array
     */
    private function filterPostData(PlaceholderInterface $model, array $rawData)
    {
        if ($rawData['position_predefined']) {
            $unset = [PlaceholderInterface::LAYOUT_POSITION];
        } else {
            $unset = [
                PlaceholderInterface::POSITION_LAYOUT,
                PlaceholderInterface::POSITION_CONTAINER,
                PlaceholderInterface::POSITION_BEFORE,
                PlaceholderInterface::POSITION_AFTER,
            ];
        }

        foreach ($unset as $key) {
            if (isset($rawData[$key])) {
                unset($rawData[$key]);
            }
        }

        $rule = $model->getRule();

        if (isset($rawData['rule']) && isset($rawData['rule']['conditions'])) {
            $rule->loadPost(['conditions' => $rawData['rule']['conditions']]);

            $conditions = $rule->getConditions()->asArray();

            if (CompatibilityService::is21()) {
                /** mp comment start **/
                $conditions = serialize($conditions);
                /** mp comment end **/

                /** mp uncomment start
                 * $conditions = "a:0:{}";
                 * mp uncomment end **/
            } else {
                $conditions = \Zend_Json::encode($conditions);
            }

            $rawData[PlaceholderInterface::CONDITIONS_SERIALIZED] = $conditions;
        } else {
            $rawData[PlaceholderInterface::CONDITIONS_SERIALIZED] = \Zend_Json::encode([]);
        }

        return $rawData;
    }
}
