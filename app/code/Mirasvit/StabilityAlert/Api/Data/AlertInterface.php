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



namespace Mirasvit\StabilityAlert\Api\Data;

interface AlertInterface
{
    const NAME        = 'name';
    const DESCRIPTION = 'description';
    const IMPORTANCE  = 'importance';
    const STATUS      = 'status';

    const IMPORTANCE_MINOR    = 1;
    const IMPORTANCE_NORMAL   = 2;
    const IMPORTANCE_MAJOR    = 3;
    const IMPORTANCE_CRITICAL = 4;

    const STATUS_SUCCESS = 'success';
    const STATUS_WARING  = 'warning';
    const STATUS_ERROR   = 'error';
    const STATUS_NA      = 'na';

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return int
     */
    public function getImportance();

    /**
     * @return string
     */
    public function getStatus();
}