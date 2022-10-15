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



namespace Mirasvit\Banner\Service;

use Mirasvit\Banner\Api\Data\PlaceholderInterface;
use Mirasvit\Banner\Block\Placeholder;

class LayoutService
{
    /**
     * @param string $handle
     * @param string $container
     * @param string $before
     * @param string $after
     *
     * @return string
     */
    public function encode($handle, $container, $before = '', $after = '')
    {
        return implode('/', [$handle, $container, $before, $after]);
    }

    /**
     * @param string $position
     *
     * @return array
     */
    public function decode($position)
    {
        $arr = explode('/', $position);

        return [
            'handle'    => isset($arr[0]) ? $arr[0] : '',
            'container' => isset($arr[1]) ? $arr[1] : '',
            'before'    => isset($arr[2]) ? $arr[2] : '',
            'after'     => isset($arr[3]) ? $arr[3] : '',
        ];
    }

    public function getXml(PlaceholderInterface $placeholder)
    {
        $name = 'mst_banner__placeholder' . $placeholder->getId();

        $position = $this->decode($placeholder->getLayoutPosition());

        $before = $position['before'] ? 'before="' . $position['before'] . '"' : '';
        $after  = $position['after'] ? 'after="' . $position['after'] . '"' : '';

        $xml   = [];
        $xml[] = '<body>';
        $xml[] = sprintf('<referenceContainer name="%s">', $position['container']);
        $xml[] = sprintf('<block class="%s" name="%s" %s %s>', Placeholder::class, $name, $before, $after);
        $xml[] = '<action method="setData">';
        $xml[] = sprintf('<argument name="name" xsi:type="string">%s</argument>', PlaceholderInterface::ID);
        $xml[] = sprintf('<argument name="value" xsi:type="number">%s</argument>', $placeholder->getId());
        $xml[] = '</action>';
        $xml[] = '</block>';
        $xml[] = '</referenceContainer>';

        $xml[] = '</body>';

        return implode('', $xml);
    }
}
