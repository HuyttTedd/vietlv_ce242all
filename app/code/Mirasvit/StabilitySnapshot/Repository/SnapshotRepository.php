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



namespace Mirasvit\StabilitySnapshot\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\StabilitySnapshot\Api\Data\SnapshotInterface;
use Mirasvit\StabilitySnapshot\Api\Data\SnapshotInterfaceFactory as SnapshotFactory;
use Mirasvit\StabilitySnapshot\Api\Repository\SnapshotRepositoryInterface;
use Mirasvit\StabilitySnapshot\Model\ResourceModel\Snapshot\CollectionFactory;

class SnapshotRepository implements SnapshotRepositoryInterface
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
     * @var SnapshotFactory
     */
    private $factory;

    /**
     * @var array
     */
    private $environmentDataPool;

    /**
     * @var array
     */
    private $healthDataPool;

    /**
     * SnapshotRepository constructor.
     * @param EntityManager $entityManager
     * @param CollectionFactory $collectionFactory
     * @param SnapshotFactory $factory
     * @param array $environmentData
     * @param array $healthData
     */
    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        SnapshotFactory $factory,
        array $environmentData = [],
        array $healthData = []
    ) {
        $this->entityManager       = $entityManager;
        $this->collectionFactory   = $collectionFactory;
        $this->factory             = $factory;
        $this->environmentDataPool = $environmentData;
        $this->healthDataPool      = $healthData;
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
        $snapshot = $this->create();
        $snapshot = $this->entityManager->load($snapshot, $id);

        if (!$snapshot->getId()) {
            return false;
        }

        return $snapshot;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(SnapshotInterface $snapshot)
    {
        $this->entityManager->delete($snapshot);
    }

    /**
     * {@inheritdoc}
     */
    public function save(SnapshotInterface $snapshot)
    {
        return $this->entityManager->save($snapshot);
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironmentDataPool()
    {
        return $this->environmentDataPool;
    }

    /**
     * {@inheritdoc}
     */
    public function getHealthDataPool()
    {
        return $this->healthDataPool;
    }
}
