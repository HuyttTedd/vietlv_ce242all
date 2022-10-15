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



namespace Mirasvit\Banner\Placeholder;

class PopupRenderer extends AbstractRenderer
{
    public function getCode()
    {
        return 'popup';
    }

    public function getLabel()
    {
        return 'Pop Up';
    }

    public function getBlockClass()
    {
        return \Mirasvit\Banner\Block\Placeholder\PopupRenderer::class;
    }
}
