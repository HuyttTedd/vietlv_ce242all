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



namespace Mirasvit\StabilitySnapshot\Api\Data;

interface SnapshotInterface
{
    const TABLE_NAME = 'mst_stability_snapshot';

    const STATUS_RUNNING = 'running';
    const STATUS_CLOSED  = 'closed';

    const ID = 'snapshot_id';

    const STATUS = 'status';

    const ENVIRONMENT_DATA = 'environment_data';
    const ENVIRONMENT_HASH = 'environment_hash';

    const HEALTH_DATA = 'health_data';

    const NOTE = 'note';

    const CREATED_AT = 'created_at';
    const CLOSED_AT  = 'closed_at';
    const UPDATED_AT = 'updated_at';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return array
     */
    public function getEnvironmentData();

    /**
     * @param array $value
     *
     * @return $this
     */
    public function setEnvironmentData($value);

    /**
     * @return string
     */
    public function getEnvironmentHash();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setEnvironmentHash($value);

    /**
     * @return array
     */
    public function getHealthData();

    /**
     * @param array $value
     *
     * @return $this
     */
    public function setHealthData($value);

    /**
     * @return string
     */
    public function getNote();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setNote($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @return string
     */
    public function getClosedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setClosedAt($value);
}
