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



namespace Mirasvit\StabilitySnapshot\Api\Data\Snapshot;

interface HealthDataInterface
{
    const UNIT_MILLISECOND = 'millisecond';
    const UNIT_PER_1000    = 'per_1000';

    const DIRECTION_UP   = 'up';
    const DIRECTION_DOWN = 'down';

    /**
     * @return string
     */
    public function getGroupTitle();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getUnit();

    /**
     * @return string
     */
    public function getDirection();

    /**
     * @return int|string
     */
    public function getValue();

    /**
     * @return int
     */
    public function getConfidence();
}
