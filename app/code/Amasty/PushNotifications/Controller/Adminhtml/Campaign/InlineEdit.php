<?php

namespace Amasty\PushNotifications\Controller\Adminhtml\Campaign;

use Amasty\PushNotifications\Api\Data\CampaignInterface;
use Amasty\PushNotifications\Model\OptionSource\Campaign\Status;
use Magento\Backend\App\Action\Context;
use Amasty\PushNotifications\Api\CampaignRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class InlineEdit extends \Amasty\PushNotifications\Controller\Adminhtml\Campaign
{
    /**
     * @var CampaignRepositoryInterface
     */
    private $repository;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(
        Context $context,
        CampaignRepositoryInterface $repository,
        TimezoneInterface $timezone
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->timezone = $timezone;
    }

    /**
     * InlineEdit Action
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $postItems = $this->getRequest()->getParam('items', []);
        $errors = [];

        if ($postItems) {
            try {
                foreach ($postItems as $campaingId => $data) {
                    /** @var \Amasty\PushNotifications\Model\Campaign $model */
                    $model = $this->repository->getById($campaingId);
                    $data = $this->prepareData($data);
                    $model->addData($data);
                    $this->repository->save($model);
                }
            } catch (LocalizedException $e) {
                $errors = array_merge($errors, [$e->getMessage()]);
            }
        }

        return $resultJson->setData(
            [
                'messages' => $errors,
                'error'    => !empty($errors)
            ]
        );
    }

    /**
     * @param array $data
     * @return array $data
     */
    private function prepareData($data)
    {
        if (isset($data[CampaignInterface::SCHEDULED])) {
            $data[CampaignInterface::SCHEDULED] = $this->timezone
                ->date(new \DateTime($data[CampaignInterface::SCHEDULED]))
                ->format('Y-m-d H:i:s');
        }

        if (isset($data[CampaignInterface::CAMPAIGN_ID])) {
            unset($data[CampaignInterface::CAMPAIGN_ID]);
        }

        return $data;
    }
}
