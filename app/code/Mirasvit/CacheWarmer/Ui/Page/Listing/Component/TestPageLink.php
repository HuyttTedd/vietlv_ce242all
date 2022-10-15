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
 * @package   mirasvit/module-cache-warmer
 * @version   1.5.8
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */




namespace Mirasvit\CacheWarmer\Ui\Page\Listing\Component;


use Magento\Backend\Block\Template;

class TestPageLink extends Template
{
    /**
     * @var \Magento\Framework\UrlInterface 
     */
    private $urlBuilder;
    
    public function __construct(Template\Context $context, array $data = [])
    {
        $this->urlBuilder = $context->getUrlBuilder();
        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        return '<a class="action-default" href="' . $this->getTestPageUrl() . '" target="_blank">' 
            . __('Check Test Page') 
            . '</a>';
    }

    private function getTestPageUrl() {
        return $this->urlBuilder->getBaseUrl() . 'cache_warmer/test/cacheable';
    }
}
