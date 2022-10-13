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



namespace Mirasvit\StabilitySnapshot\Ui;

use Mirasvit\Stability\Ui\General\Toolbar\ButtonInterface;

class ToolbarButton implements ButtonInterface
{
    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getLabel()
    {
        return __('Performance');
    }

    /**
     * @return bool|false|int
     */
    public function getExtra()
    {
        return false;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return 20;
    }

    /**
     * @return string
     */
    public function getUiName()
    {
        return 'stability_snapshot_listing';
    }
}
