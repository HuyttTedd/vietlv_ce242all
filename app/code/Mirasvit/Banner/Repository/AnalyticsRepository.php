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
 * @package   mirasvit/module-banner
 * @version   1.0.11
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Banner\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\Banner\Api\Data\AnalyticsInterface;
use Mirasvit\Banner\Api\Data\AnalyticsInterfaceFactory;
use Mirasvit\Banner\Model\ResourceModel\Analytics\CollectionFactory;

class AnalyticsRepository
{
    private $entityManager;

    private $collectionFactory;

    private $factory;

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        AnalyticsInterfaceFactory $factory
    ) {
        $this->entityManager     = $entityManager;
        $this->collectionFactory = $collectionFactory;
        $this->factory           = $factory;
    }

    /**
     * @return AnalyticsInterface[]|\Mirasvit\Banner\Model\ResourceModel\Analytics\Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @return AnalyticsInterface
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * @param int $id
     *
     * @return AnalyticsInterface|false
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
     * @param AnalyticsInterface $model
     *
     * @return AnalyticsInterface
     */
    public function save(AnalyticsInterface $model)
    {
        return $this->entityManager->save($model);
    }

    /**
     * @param AnalyticsInterface $model
     */
    public function delete(AnalyticsInterface $model)
    {
        $this->entityManager->delete($model);
    }
}
