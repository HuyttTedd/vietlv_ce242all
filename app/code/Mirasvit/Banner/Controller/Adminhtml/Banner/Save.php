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



namespace Mirasvit\Banner\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Mirasvit\Banner\Api\Data\BannerInterface;
use Mirasvit\Banner\Controller\Adminhtml\AbstractBanner;
use Mirasvit\Banner\Repository\BannerRepository;
use Mirasvit\Core\Service\CompatibilityService;

class Save extends AbstractBanner
{
    /**
     * @var Date
     */
    private $dateFilter;

    public function __construct(
        Date $dateFilter,
        BannerRepository $bannerRepository,
        Registry $registry,
        Context $context
    ) {
        $this->dateFilter = $dateFilter;

        parent::__construct($bannerRepository, $registry, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id             = $this->getRequest()->getParam(BannerInterface::ID);
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getParams();

        if ($data) {
            $model = $this->initModel();
            $data  = $this->filterPostData($data, $model);

            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This banner no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            foreach ($data as $key => $value) {
                $model->setDataUsingMethod($key, $value);
            }

            try {
                $this->bannerRepository->save($model);

                $this->messageManager->addSuccessMessage(__('You have saved the banner.'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', [BannerInterface::ID => $model->getId()]);
                }

                return $this->context->getResultRedirectFactory()->create()->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath(
                    '*/*/edit',
                    [BannerInterface::ID => $this->getRequest()->getParam(BannerInterface::ID)]
                );
            }
        } else {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addErrorMessage('No data to save.');

            return $resultRedirect;
        }
    }

    /**
     * @param array           $rawData
     * @param BannerInterface $model
     *
     * @return array
     */
    private function filterPostData(array $rawData, BannerInterface $model)
    {
        $data = $rawData;

        $rule = $model->getRule();

        if (isset($data['rule']) && isset($data['rule']['conditions'])) {
            $rule->loadPost(['conditions' => $data['rule']['conditions']]);

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

            $data[BannerInterface::CONDITIONS_SERIALIZED] = $conditions;
        } else {
            $data[BannerInterface::CONDITIONS_SERIALIZED] = \Zend_Json::encode([]);
        }

        $filterRules = [];
        foreach ([BannerInterface::ACTIVE_FROM, BannerInterface::ACTIVE_TO] as $dateField) {
            if (!empty($data[$dateField])) {
                $filterRules[$dateField] = $this->dateFilter;
            }
        }
        $data = (new \Zend_Filter_Input($filterRules, [], $data))->getUnescaped();

        return $data;
    }
}
