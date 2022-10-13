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



namespace Mirasvit\StabilitySnapshot\Repository\EnvironmentData;

use Magento\Framework\App\Area;
use Magento\Framework\ObjectManager\Config\Reader\Dom as DomReader;
use Mirasvit\StabilitySnapshot\Api\Data\Snapshot\EnvironmentDataInterface;
use Zend\Server\Reflection;

class PluginData implements EnvironmentDataInterface
{
    /**
     * @var DomReader
     */
    private $reader;

    /**
     * PluginData constructor.
     * @param DomReader $reader
     */
    public function __construct(
        DomReader $reader
    ) {
        $this->reader = $reader;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Plugins';
    }

    /**
     * @return array
     */
    public function capture()
    {
        $data = [];

        $areas = [
            Area::AREA_DOC,
            Area::AREA_CRONTAB,
            Area::AREA_ADMINHTML,
            Area::AREA_GLOBAL,
            Area::AREA_FRONTEND,
            Area::AREA_WEBAPI_REST,
            Area::AREA_WEBAPI_SOAP,
        ];

        foreach ($areas as $area) {
            foreach ($this->reader->read($area) as $config) {
                if (empty($config['plugins'])) {
                    continue;
                }

                foreach ($config['plugins'] as $plugin) {
                    $reflection = $this->getPluginReflection($plugin);
                    if (!$reflection) {
                        continue;
                    }

                    $instance        = $reflection->getName();
                    $data[$instance] = [
                        'value' => $instance,
                    ];
                }
            }
        }

        return $data;
    }

    /**
     * @param array $observer
     *
     * @return null|\Zend\Server\Reflection\ReflectionClass
     */
    private function getPluginReflection(array $observer)
    {
        if (empty($observer['instance'])) {
            return null;
        }

        try {
            return Reflection::reflectClass($observer['instance']);
        } catch (\Exception $e) {
            return null;
        }
    }
}
