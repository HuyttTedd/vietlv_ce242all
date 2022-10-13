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
use Mirasvit\Banner\Api\Data\BannerInterface;
use Mirasvit\Banner\Api\Data\BannerInterfaceFactory;
use Mirasvit\Banner\Model\ResourceModel\Banner\CollectionFactory;

class BannerRepository
{
    private $entityManager;

    private $collectionFactory;

    private $factory;

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        BannerInterfaceFactory $factory
    ) {
        $this->entityManager     = $entityManager;
        $this->collectionFactory = $collectionFactory;
        $this->factory           = $factory;
    }

    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    public function create()
    {
        return $this->factory->create();
    }

    /**
     * @param int $id
     *
     * @return bool|BannerInterface
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

    public function delete(BannerInterface $model)
    {
        $this->entityManager->delete($model);
    }

    public function save(BannerInterface $model)
    {
        return $this->entityManager->save($model);
    }
}
