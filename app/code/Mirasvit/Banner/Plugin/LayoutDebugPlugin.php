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



namespace Mirasvit\Banner\Plugin;

use Magento\Framework\View\Layout;
use Magento\Framework\View\Layout\Data\Structure;
use Mirasvit\Banner\Model\ConfigProvider;

/**
 * @see \Magento\Framework\View\Layout::renderElement()
 */
class LayoutDebugPlugin
{
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * @param Layout $subject
     * @param string $name
     * @param bool   $useCache
     *
     * @return array
     */
    public function beforeRenderElement($subject, $name, $useCache = true)
    {
        if (!$this->configProvider->isDebug()) {
            return [$name, $useCache];
        }

//        $structure = $this->getStructure($subject);

        if ($subject->isContainer($name)) {
            $block = $subject->addBlock(\Magento\Framework\View\Element\Template::class, null, $name);
            $block->setTemplate('Mirasvit_Banner::debug/container.phtml')
                ->setData('name', $name);
            //
            //            $class = $structure->getAttribute($name, Layout\Element::CONTAINER_OPT_HTML_CLASS);
            //            $class = $class . ' mst-banner__container-debug ';
            //            $structure->setAttribute($name, Layout\Element::CONTAINER_OPT_HTML_CLASS, $class);
        }

        return [$name, $useCache];
    }

//    /**
//     * @param Layout $layout
//     *
//     * @return Structure
//     * @throws \ReflectionException
//     */
//    private function getStructure(Layout $layout)
//    {
//        $reflection = new \ReflectionProperty($layout, 'structure');
//        $reflection->setAccessible(true);
//
//        return $reflection->getValue($layout);
//    }
}
