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



namespace Mirasvit\Banner\Block\Placeholder;

class CustomRenderer extends AbstractRenderer
{
    protected $_template = 'Mirasvit_Banner::placeholder/rotatorRenderer.phtml';

    public function toHtml()
    {
        $this->_template = $this->getPlaceholder()->getRenderer();

        return parent::toHtml();
    }

    /**
     * @param int $limit
     *
     * @return \Mirasvit\Banner\Api\Data\BannerInterface[]
     */
    public function getBanners($limit = 1)
    {
        return $this->bannerService->getApplicableBanners($this->getPlaceholder(), $limit);
    }
}
