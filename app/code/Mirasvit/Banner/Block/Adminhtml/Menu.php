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



namespace Mirasvit\Banner\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Mirasvit\Core\Block\Adminhtml\AbstractMenu;

class Menu extends AbstractMenu
{
    public function __construct(
        Context $context
    ) {
        $this->visibleAt(['mst_banner']);

        parent::__construct($context);
    }

    protected function buildMenu()
    {
        $this->addItem([
            'resource' => 'Mirasvit_Banner::banner_banner',
            'title'    => __('Banners'),
            'url'      => $this->urlBuilder->getUrl('mst_banner/banner/index'),
        ])->addItem([
            'resource' => 'Mirasvit_Banner::banner_placeholder',
            'title'    => __('Placeholders'),
            'url'      => $this->urlBuilder->getUrl('mst_banner/placeholder/index'),
        ]);;

        return $this;
    }
}
