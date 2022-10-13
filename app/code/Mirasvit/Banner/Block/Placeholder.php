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



namespace Mirasvit\Banner\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Banner\Api\Data\PlaceholderInterface;
use Mirasvit\Banner\Model\ConfigProvider;
use Mirasvit\Banner\Repository\PlaceholderRepository;
use Mirasvit\Banner\Service\BannerService;
use Mirasvit\Banner\Service\PlaceholderService;

class Placeholder extends Template
{
    /** @var string */
    protected $_template = 'Mirasvit_Banner::placeholder.phtml';

    private   $placeholderRepository;

    private   $bannerService;

    private   $placeholderService;

    private   $configProvider;

    public function __construct(
        PlaceholderRepository $placeholderRepository,
        BannerService $bannerService,
        PlaceholderService $placeholderService,
        ConfigProvider $configProvider,
        Context $context
    ) {
        $this->placeholderRepository = $placeholderRepository;
        $this->bannerService         = $bannerService;
        $this->placeholderService    = $placeholderService;
        $this->configProvider        = $configProvider;

        parent::__construct($context);
    }

    public function toHtml()
    {
        $placeholder = $this->getPlaceholder();

        if (!$placeholder) {
            return '';
        }

        if (!$placeholder->isActive() && !$this->configProvider->isDebug()) {
            return '';
        }

        if (!$this->placeholderService->isApplicable($placeholder) && !$this->configProvider->isDebug()) {
            return '';
        }

        return parent::toHtml();
    }

    /**
     * @return PlaceholderInterface
     */
    public function getPlaceholder()
    {
        return $this->placeholderRepository->get($this->getData(PlaceholderInterface::ID));
    }

    public function isDebug()
    {
        return $this->configProvider->isDebug();
    }

    public function getRendererHtml()
    {
        if ($this->bannerService->isAjaxRequired($this->getPlaceholder())) {
            return '';
        }

        $placeholder = $this->getPlaceholder();

        $render = $this->placeholderRepository->getRenderer($placeholder);

        /** @var Placeholder\AbstractRenderer $block */
        $block = $this->_layout->createBlock($render->getBlockClass());
        $block->setData(PlaceholderInterface::class, $placeholder);

        return $block->toHtml();
    }
}
