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



namespace Mirasvit\Stability\Api\Repository;

interface StorageRepositoryInterface
{
    /**
     * @param string $class
     * @param string $method
     * @param array $data
     * @return mixed
     */
    public function put($class, $method, array $data);

    /**
     * @param string     $code
     * @param int|string $value
     *
     * @return $this
     */
    public function setValue($code, $value);

    /**
     * @param string $code
     * @param bool   $default
     *
     * @return int|string
     */
    public function getValue($code, $default = false);

    /**
     * @param string     $code
     * @param int|string $value
     *
     * @return $this
     */
    public function setSnapshotValue($code, $value);

    /**
     * @param string $code
     * @param bool|int   $default
     *
     * @return int|string
     */
    public function getSnapshotValue($code, $default = false);
}
