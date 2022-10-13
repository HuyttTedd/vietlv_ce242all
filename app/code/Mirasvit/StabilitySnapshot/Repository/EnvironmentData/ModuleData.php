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

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Module\ModuleList;
use Mirasvit\StabilitySnapshot\Api\Data\Snapshot\EnvironmentDataInterface;

class ModuleData implements EnvironmentDataInterface
{
    /**
     * @var ModuleList
     */
    private $moduleList;

    /**
     * @var ComponentRegistrarInterface
     */
    private $componentRegistrar;

    /**
     * @var ReadFactory
     */
    private $readFactory;

    /**
     * ModuleData constructor.
     * @param ModuleList $moduleList
     * @param ComponentRegistrarInterface $componentRegistrar
     * @param ReadFactory $readFactory
     */
    public function __construct(
        ModuleList $moduleList,
        ComponentRegistrarInterface $componentRegistrar,
        ReadFactory $readFactory
    ) {
        $this->moduleList         = $moduleList;
        $this->componentRegistrar = $componentRegistrar;
        $this->readFactory        = $readFactory;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Modules';
    }

    /**
     * @return array
     */
    public function capture()
    {
        $data = [];

        foreach ($this->moduleList->getAll() as $module) {
            $name = $module['name'];

            $composerVersion = $this->getComposerVersion($name);

            $data[$name] = [
                'label' => $name,
                'value' => $composerVersion ? $composerVersion : $module['setup_version'],
            ];
        }

        return $data;
    }

    /**
     * @param string $moduleName
     *
     * @return string|null
     */
    private function getComposerVersion($moduleName)
    {
        try {
            $path = $this->componentRegistrar->getPath(
                ComponentRegistrar::MODULE,
                $moduleName
            );

            $directoryRead    = $this->readFactory->create($path);
            $composerJsonData = $directoryRead->readFile('composer.json');
            $data             = \Zend_Json::decode($composerJsonData);

            return isset($data['version']) ? $data['version'] : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
