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



namespace Mirasvit\StabilityError\Api\Data;

interface ErrorInterface
{
    const TABLE_NAME = 'mst_stability_error';

    const TYPE_PHP_EXCEPTION = 'exception';
    const TYPE_PHP_ERROR     = 'error';
    const TYPE_JS_ERROR      = 'jsError';

    const ID          = 'error_id';
    const SNAPSHOT_ID = 'snapshot_id';

    const IDENTIFIER = 'identifier';
    const TYPE       = 'type';
    const URI        = 'uri';
    const MESSAGE    = 'message';
    const TRACE      = 'trace';
    const COUNT      = 'count';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getSnapshotId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setSnapshotId($value);

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setIdentifier($value);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setType($value);

    /**
     * @return string
     */
    public function getUri();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUri($value);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMessage($value);

    /**
     * @return string
     */
    public function getTrace();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTrace($value);

    /**
     * @return int
     */
    public function getCount();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setCount($value);

    /**
     * @return string
     */
    public function getCreatedAt();
}