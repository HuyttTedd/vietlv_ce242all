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



namespace Mirasvit\StabilitySnapshot\Service;

use Mirasvit\StabilitySnapshot\Api\Data\SnapshotInterface;
use Mirasvit\StabilitySnapshot\Api\Repository\SnapshotRepositoryInterfaceFactory;
use Mirasvit\StabilitySnapshot\Api\Service\SnapshotServiceInterface;

class SnapshotService implements SnapshotServiceInterface
{
    /**
     * @var SnapshotRepositoryInterfaceFactory
     */
    private $snapshotRepositoryFactory;

    /**
     * @var EnvironmentDataServiceFactory
     */
    private $environmentDataServiceFactory;

    /**
     * @var HealthDataServiceFactory
     */
    private $healthDataServiceFactory;

    /**
     * SnapshotService constructor.
     * @param SnapshotRepositoryInterfaceFactory $snapshotRepositoryFactory
     * @param EnvironmentDataServiceFactory $environmentDataServiceFactory
     * @param HealthDataServiceFactory $healthDataServiceFactory
     */
    public function __construct(
        SnapshotRepositoryInterfaceFactory $snapshotRepositoryFactory,
        EnvironmentDataServiceFactory $environmentDataServiceFactory,
        HealthDataServiceFactory $healthDataServiceFactory
    ) {
        $this->snapshotRepositoryFactory     = $snapshotRepositoryFactory;
        $this->environmentDataServiceFactory = $environmentDataServiceFactory;
        $this->healthDataServiceFactory      = $healthDataServiceFactory;
    }

    /**
     * @return SnapshotInterface
     */
    public function getRunningSnapshot()
    {
        /** @var SnapshotInterface $snapshot */
        $snapshot = $this->snapshotRepositoryFactory->create()->getCollection()
            ->addFieldToFilter(SnapshotInterface::STATUS, SnapshotInterface::STATUS_RUNNING)
            ->getFirstItem();

        if ($snapshot->getId()) {
            return $snapshot;
        } else {
            $snapshot = $this->snapshotRepositoryFactory->create()->create();
            $snapshot->setStatus(SnapshotInterface::STATUS_RUNNING);

            return $snapshot;
        }
    }

    /**
     * @return SnapshotInterface|false
     */
    public function getLastClosedSnapshot()
    {
        /** @var SnapshotInterface $snapshot */
        $snapshot = $this->snapshotRepositoryFactory->create()->getCollection()
            ->addFieldToFilter(SnapshotInterface::STATUS, SnapshotInterface::STATUS_CLOSED)
            ->setOrder(SnapshotInterface::ID, 'desc')
            ->getFirstItem();
        if ($snapshot->getId()) {
            return $snapshot;
        }

        return false;
    }

    /**
     * @return SnapshotInterface
     */
    public function makeSnapshot()
    {
        $healthData      = $this->healthDataServiceFactory->create()->capture();
        $environmentData = $this->environmentDataServiceFactory->create()->capture();

        $newSnapshot = $this->snapshotRepositoryFactory->create()->create();
        $newSnapshot->setEnvironmentData($environmentData)
            ->setHealthData($healthData)
            ->setStatus(SnapshotInterface::STATUS_RUNNING);

        return $newSnapshot;
    }

    /**
     * @param SnapshotInterface $snapshot
     *
     * @return SnapshotInterface
     */
    public function closeSnapshot(SnapshotInterface $snapshot)
    {
        $snapshot->setStatus(SnapshotInterface::STATUS_CLOSED)
            ->setClosedAt(\Zend_Date::now()->toString(\Zend_Date::ISO_8601));

        return $snapshot;
    }

    /**
     * @param SnapshotInterface $closed
     * @param SnapshotInterface $running
     * @return array
     */
    public function compare(SnapshotInterface $closed, SnapshotInterface $running)
    {
        return $this->environmentDataServiceFactory->create()->compare($closed, $running);
    }
}
