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



namespace Mirasvit\Banner\Service;

use Magento\Framework\View\LayoutInterface;
use Mirasvit\Banner\Api\Data\PlaceholderInterface;
use Mirasvit\Banner\Repository\PlaceholderRepository;

class PlaceholderService
{
    private $layout;

    private $placeholderRepository;

    public function __construct(
        LayoutInterface $layout,
        PlaceholderRepository $placeholderRepository
    ) {
        $this->layout                = $layout;
        $this->placeholderRepository = $placeholderRepository;
    }

    public function getRendererHtml(PlaceholderInterface $placeholder)
    {
        $render = $this->placeholderRepository->getRenderer($placeholder);

        /** @var \Mirasvit\Banner\Block\Placeholder\AbstractRenderer $block */
        $block = $this->layout->createBlock($render->getBlockClass());
        $block->setData(PlaceholderInterface::class, $placeholder);

        return $block->toHtml();
    }

    public function isApplicable(PlaceholderInterface $placeholder)
    {
        $dataObject = $placeholder->getRule()->getDataObject([]);
        $dataObject->init();

        return $placeholder->getRule()->validate($dataObject);
    }
}
