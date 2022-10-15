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



namespace Mirasvit\StabilityError\Api\Repository;

use Mirasvit\StabilityError\Api\Data\ErrorInterface;

interface ErrorRepositoryInterface
{
    /**
     * @return \Mirasvit\StabilityError\Model\ResourceModel\Error\Collection|ErrorInterface[]
     */
    public function getCollection();

    /**
     * @return ErrorInterface
     */
    public function create();

    /**
     * @param ErrorInterface $model
     *
     * @return ErrorInterface
     */
    public function save(ErrorInterface $model);

    /**
     * @param int $id
     *
     * @return ErrorInterface|false
     */
    public function get($id);

    /**
     * @param ErrorInterface $model
     *
     * @return bool
     */
    public function delete(ErrorInterface $model);
}
