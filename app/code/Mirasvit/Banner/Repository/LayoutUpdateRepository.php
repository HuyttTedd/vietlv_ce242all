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

use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory as ThemeCollectionFactory;
use Magento\Widget\Model\Layout\Update as LayoutUpdate;
use Magento\Widget\Model\Layout\UpdateFactory as LayoutUpdateFactory;
use Magento\Widget\Model\ResourceModel\Layout\Link\CollectionFactory as LinkCollectionFactory;
use Mirasvit\Banner\Api\Data\PlaceholderInterface;
use Mirasvit\Banner\Model\ConfigProvider;
use Mirasvit\Banner\Service\LayoutService;

class LayoutUpdateRepository
{
    private $layoutUpdateFactory;

    private $linkCollectionFactory;

    private $layoutService;

    private $themeCollectionFactory;

    private $configProvider;

    public function __construct(
        LayoutUpdateFactory $layoutUpdateFactory,
        LinkCollectionFactory $linkCollectionFactory,
        LayoutService $layoutService,
        ThemeCollectionFactory $themeCollectionFactory,
        ConfigProvider $config
    ) {
        $this->layoutUpdateFactory    = $layoutUpdateFactory;
        $this->linkCollectionFactory  = $linkCollectionFactory;
        $this->layoutService          = $layoutService;
        $this->themeCollectionFactory = $themeCollectionFactory;
        $this->configProvider         = $config;
    }

    public function save(PlaceholderInterface $placeholder)
    {
        $layoutUpdate = $this->getLayoutUpdate($placeholder->getLayoutUpdateId());

        $position = $this->layoutService->decode($placeholder->getLayoutPosition());

        $layoutUpdate->setData('handle', $position['handle'])
            ->setData('xml', $this->layoutService->getXml($placeholder));
        $layoutUpdate->save();

        $placeholder->setLayoutUpdateId($layoutUpdate->getId());

        $this->ensureLinks($placeholder, $layoutUpdate);

        return $placeholder;
    }

    public function delete(PlaceholderInterface $block)
    {
        $update = $this->getLayoutUpdate($block->getLayoutUpdateId());
        $update->delete();
    }

    private function ensureLinks(PlaceholderInterface $block, LayoutUpdate $layoutUpdate)
    {
        $links = $this->linkCollectionFactory->create();
        $links->addFieldToFilter('layout_update_id', $layoutUpdate->getId());
        foreach ($links as $link) {
            $link->delete();
        }

        $themes = $this->themeCollectionFactory->create()
            ->addAreaFilter('frontend');

        /** @var \Magento\Theme\Model\Theme $theme */
        foreach ($themes as $theme) {
            $layoutUpdate->setData('store_id', 0)
                ->setData('theme_id', $theme->getId())
                ->save();
        }
    }

    /**
     * @param int $updateId
     *
     * @return LayoutUpdate
     */
    private function getLayoutUpdate($updateId)
    {
        $layoutUpdate = $this->layoutUpdateFactory->create();

        if ($updateId) {
            $layoutUpdate->load($updateId);
        }

        return $layoutUpdate;
    }
}
