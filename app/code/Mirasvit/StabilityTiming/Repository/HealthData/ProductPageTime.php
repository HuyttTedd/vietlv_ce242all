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



namespace Mirasvit\StabilityTiming\Repository\HealthData;

class ProductPageTime extends AbstractPageTime
{
    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTitle()
    {
        return __('Product Page');
    }

    /**
     * @return array|string[]
     */
    protected function getActions()
    {
        return ['catalog_product_view'];
    }
}
