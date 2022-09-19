<?php

namespace Amasty\PushNotifications\Model;

use Amasty\PushNotifications\Api\Data;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Amasty\PushNotifications\Api\CampaignStoreRepositoryInterface;
use Amasty\PushNotifications\Model\ResourceModel\CampaignStore as CampaignStoreResource;
use Amasty\PushNotifications\Model\CampaignStoreFactory;

class CampaignStoreRepository implements CampaignStoreRepositoryInterface
{
    /**
     * @var CampaignStoreResource
     */
    protected $resource;

    /**
     * @var \Amasty\PushNotifications\Model\CampaignStoreFactory
     */
    protected $factory;

    /**
     * CampaignStoreRepository constructor.
     *
     * @param CampaignStoreResource $resource
     * @param \Amasty\PushNotifications\Model\CampaignStoreFactory $factory
     */
    public function __construct(
        CampaignStoreResource $resource,
        CampaignStoreFactory $factory
    ) {
        $this->resource = $resource;
        $this->factory = $factory;
    }

    /**
     * @param Data\CampaignStoreInterface $item
     *
     * @return Data\CampaignStoreInterface
     *
     * @throws CouldNotSaveException
     */
    public function save(Data\CampaignStoreInterface $item)
    {
        try {
            $this->resource->save($item);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $item;
    }

    /**
     * @param $id
     *
     * @return Data\CampaignStoreInterface
     *
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $model = $this->factory->create();
        $this->resource->load($model, $id);
        if (!$model->getId()) {
            throw new NoSuchEntityException(__('Campaign Store with id "%1" does not exist.', $id));
        }

        return $model;
    }

    /**
     * @param Data\CampaignStoreInterface $item
     *
     * @return bool
     *
     * @throws CouldNotDeleteException
     */
    public function delete(Data\CampaignStoreInterface $item)
    {
        try {
            $this->resource->delete($item);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the campaign store: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * @param int $id
     *
     * @return bool
     *
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
