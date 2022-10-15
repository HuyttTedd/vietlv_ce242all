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



namespace Mirasvit\StabilityAlert\Repository\Alert;

use Magento\Framework\App\DeploymentConfig\Reader;
use Magento\Framework\App\State;
use Mirasvit\StabilityAlert\Api\Data\AlertInterface;

class DeployModeAlert implements AlertInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * DeployModeAlert constructor.
     * @param Reader $reader
     */
    public function __construct(
        Reader $reader
    ) {
        $this->reader = $reader;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getName()
    {
        return __('Deploy Mode');
    }

    /**
     * @return int
     */
    public function getImportance()
    {
        return self::IMPORTANCE_MAJOR;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\RuntimeException
     */
    public function getDescription()
    {
        $list = [];

        if ($this->isProduction()) {
            $list[] = __('Magento in Production Mode.');
        } else {
            $list[] = __('Run command `bin/magento deploy:mode:set production` to switch to Magento in Production Mode');
        }

        return implode(' ', $list);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\RuntimeException
     */
    public function getStatus()
    {
        return $this->isProduction() ? self::STATUS_SUCCESS : self::STATUS_ERROR;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\RuntimeException
     */
    private function isProduction()
    {
        $env  = $this->reader->load();
        $mode = isset($env[State::PARAM_MODE]) ? $env[State::PARAM_MODE] : null;

        return $mode === 'production' ? true : false;
    }
}
