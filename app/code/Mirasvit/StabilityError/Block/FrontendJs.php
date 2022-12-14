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



namespace Mirasvit\StabilityError\Block;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\Template;

class FrontendJs extends Template
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * FrontendJs constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->urlBuilder = $context->getUrlBuilder();

        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getMageInit()
    {
        $baseUrl = $this->urlBuilder->getUrl('stability_error/handle/jsError');

        return [
            'Mirasvit_StabilityError/js/frontend' => [
                'url' => $baseUrl,
            ],
        ];
    }
}
