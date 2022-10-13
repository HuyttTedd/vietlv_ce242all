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



namespace Mirasvit\Banner\Ui\Placeholder\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Layout\PageType\Config as LayoutConfig;

class LayoutSource implements OptionSourceInterface
{
    private $layoutConfig;

    public function __construct(
        LayoutConfig $layoutConfig
    ) {
        $this->layoutConfig = $layoutConfig;
    }

    public function toOptionArray()
    {
        $options = [
            [
                'value' => 'default',
                'label' => __('All Pages'),
            ],
        ];

        foreach ($this->layoutConfig->getPageTypes() as $type) {
            $options[] = [
                'value' => $type->getData('id'),
                'label' => $type->getData('label'),
            ];
        }

        return $options;
    }
}
