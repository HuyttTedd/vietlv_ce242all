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



namespace Mirasvit\StabilitySnapshot\Api\Repository;

use Mirasvit\StabilitySnapshot\Api\Data\Snapshot\EnvironmentDataInterface;
use Mirasvit\StabilitySnapshot\Api\Data\Snapshot\HealthDataInterface;
use Mirasvit\StabilitySnapshot\Api\Data\SnapshotInterface;

interface SnapshotRepositoryInterface
{
    /**
     * @return \Mirasvit\StabilitySnapshot\Model\ResourceModel\Snapshot\Collection|SnapshotInterface[]
     */
    public function getCollection();

    /**
     * @return SnapshotInterface
     */
    public function create();

    /**
     * @param SnapshotInterface $snapshot
     *
     * @return SnapshotInterface
     */
    public function save(SnapshotInterface $snapshot);

    /**
     * @param int $id
     *
     * @return SnapshotInterface|false
     */
    public function get($id);

    /**
     * @param SnapshotInterface $snapshot
     *
     * @return bool
     */
    public function delete(SnapshotInterface $snapshot);

    /**
     * @return EnvironmentDataInterface[]
     */
    public function getEnvironmentDataPool();

    /**
     * @return HealthDataInterface[]
     */
    public function getHealthDataPool();
}
