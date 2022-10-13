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

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Loader extends Template
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Context $context,
        Registry $registry
    ) {
        $this->registry = $registry;

        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getJsConfig()
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->_request;

        $product  = $this->registry->registry('current_product');
        $category = $this->registry->registry('current_category');

        return [
            '*' => [
                'Mirasvit_Banner/js/loader'    => [
                    'url'    => $this->getUrl('mst_banner/placeholder/loader'),
                    'params' => [
                        'page_type'   => $request->getFullActionName(),
                        'product_id'  => $product ? $product->getId() : null,
                        'category_id' => $category ? $category->getId() : null,
                        'uri'         => $request->getUriString(),
                    ],
                ],
                'Mirasvit_Banner/js/analytics' => [
                    'url'    => $this->getUrl('mst_banner/banner/track'),
                    'params' => [
                        'uri' => $request->getRequestUri(),
                    ],
                ],
            ],
        ];
    }

}
