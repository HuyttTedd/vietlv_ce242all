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
 * @package   mirasvit/module-stability
 * @version   1.1.0
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\StabilityError\Repository;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\StabilityError\Api\Data\ErrorInterface;
use Mirasvit\StabilityError\Api\Data\ErrorInterfaceFactory;
use Mirasvit\StabilityError\Api\Repository\ErrorRepositoryInterface;
use Mirasvit\StabilityError\Model\ResourceModel\Error\CollectionFactory;

class ErrorRepository implements ErrorRepositoryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var ErrorInterfaceFactory
     */
    private $factory;

    /**
     * ErrorRepository constructor.
     * @param EntityManager $entityManager
     * @param ResourceConnection $resource
     * @param CollectionFactory $collectionFactory
     * @param ErrorInterfaceFactory $factory
     */
    public function __construct(
        EntityManager $entityManager,
        ResourceConnection $resource,
        CollectionFactory $collectionFactory,
        ErrorInterfaceFactory $factory
    ) {
        $this->entityManager     = $entityManager;
        $this->resource          = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->factory           = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $model = $this->create();
        $model = $this->entityManager->load($model, $id);

        if (!$model->getId()) {
            return false;
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ErrorInterface $model)
    {
        $this->entityManager->delete($model);
    }

    /**
     * {@inheritdoc}
     */
    public function save(ErrorInterface $model)
    {
        if (!$model->getIdentifier()) {
            $model->setIdentifier(sha1($model->getMessage()));
        }

        /** @var ErrorInterface $previousError */
        $previousError = $this->getCollection()
            ->addFieldToFilter(ErrorInterface::SNAPSHOT_ID, $model->getSnapshotId())
            ->addFieldToFilter(ErrorInterface::IDENTIFIER, $model->getIdentifier())
            ->getFirstItem();

        if ($previousError->getId()) {
            $previousError->setCount($previousError->getCount() + 1);
            return $this->entityManager->save($previousError);
        } else {
            return $this->entityManager->save($model);
        }
    }
}
