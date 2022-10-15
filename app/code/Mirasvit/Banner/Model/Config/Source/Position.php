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



namespace Mirasvit\Banner\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Widget\Model\ResourceModel\Widget\Instance\Collection as WidgetCollection;
use Mirasvit\Banner\Api\Data\BannerInterface;
use Mirasvit\Banner\Block\Widget\Placeholder;

class Position implements OptionSourceInterface
{
    /**
     * @var WidgetCollection
     */
    private $widgetCollection;

    /**
     * Position constructor.
     * @param WidgetCollection $widgetCollection
     */
    public function __construct(
        WidgetCollection $widgetCollection
    ) {
        $this->widgetCollection = $widgetCollection;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [
            [
                'label' => __('Page Top'),
                'value' => BannerInterface::POSITION_PAGE_TOP,
            ],
            [
                'label' => __('Page Bottom'),
                'value' => BannerInterface::POSITION_PAGE_BOTTOM,
            ],
        ];

        $collection = $this->widgetCollection
            ->addFieldToFilter('instance_type', Placeholder::class);

        /** @var \Magento\Widget\Model\Widget\Instance $item */
        foreach ($collection as $item) {
            $params = $item->getWidgetParameters();
            if (!is_array($params) || !isset($params['position'])) {
                continue;
            }

            $result[] = [
                'label' => $item->getTitle(),
                'value' => $params['position'],
            ];
        }

        return $result;
    }
}
