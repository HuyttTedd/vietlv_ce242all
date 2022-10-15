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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Filesystem;
use Magento\Framework\Module\Dir;
use Mirasvit\Banner\Api\Data\PlaceholderInterface;
use Mirasvit\Banner\Api\Data\PlaceholderInterfaceFactory;
use Mirasvit\Banner\Model\ResourceModel\Placeholder\CollectionFactory;
use Mirasvit\Banner\Placeholder\AbstractRenderer;
use Mirasvit\Banner\Placeholder\CustomRenderer;

class PlaceholderRepository
{
    private $entityManager;

    private $collectionFactory;

    private $factory;

    private $layoutUpdateRepository;

    private $filesystem;

    private $dir;

    /**
     * @var AbstractRenderer[]
     */
    private $renderer;

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        PlaceholderInterfaceFactory $factory,
        LayoutUpdateRepository $layoutUpdateRepository,
        Filesystem $filesystem,
        Dir $dir,
        array $renderer = []
    ) {
        $this->entityManager          = $entityManager;
        $this->collectionFactory      = $collectionFactory;
        $this->factory                = $factory;
        $this->layoutUpdateRepository = $layoutUpdateRepository;
        $this->renderer               = $renderer;
        $this->filesystem             = $filesystem;
        $this->dir                    = $dir;
    }

    /**
     * @return PlaceholderInterface[]|\Mirasvit\Banner\Model\ResourceModel\Placeholder\Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @return PlaceholderInterface
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * @param int $id
     *
     * @return PlaceholderInterface|false
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
     * @param PlaceholderInterface $model
     *
     * @return PlaceholderInterface
     */
    public function save(PlaceholderInterface $model)
    {
        if (!$model->getId()) {
            $this->entityManager->save($model);
        }

        $model = $this->layoutUpdateRepository->save($model);

        return $this->entityManager->save($model);
    }

    /**
     * @param PlaceholderInterface $model
     */
    public function delete(PlaceholderInterface $model)
    {
        $this->layoutUpdateRepository->delete($model);
        $this->entityManager->delete($model);
    }

    public function getRenderers()
    {
        $appDir = $this->filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath();
        $toScan = [
            'Mirasvit_Banner' => [
                $appDir . 'design/frontend/*/*/Mirasvit_Banner/templates/placeholder/*.phtml',
            ],
        ];

        foreach ($toScan as $module => $dirs) {
            foreach ($dirs as $dirPattern) {
                foreach (glob($dirPattern) as $filename) {
                    $basename = pathinfo($filename)['basename'];
                    $name     = pathinfo($filename)['filename'];
                    $code     = $module . '::placeholder/' . $basename;

                    $customRenderer = new CustomRenderer();
                    $customRenderer->setCode($code)
                        ->setLabel(ucfirst($name));

                    $this->renderer[$code] = $customRenderer;
                }
            }
        }

        return $this->renderer;
    }

    /**
     * @param PlaceholderInterface $placeholder
     *
     * @return AbstractRenderer
     */
    public function getRenderer(PlaceholderInterface $placeholder)
    {
        return $this->getRenderers()[$placeholder->getRenderer()];
    }
}
