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

use Mirasvit\Stability\Api\Repository\StorageRepositoryInterface;
use Mirasvit\StabilitySnapshot\Api\Data\Snapshot\HealthDataInterface;
use Mirasvit\StabilityTiming\Model\Config;

abstract class AbstractPageTime implements HealthDataInterface
{
    /**
     * @var StorageRepositoryInterface
     */
    protected $storageRepository;

    /**
     * @var Config
     */
    protected $config;

    /**
     * AbstractPageTime constructor.
     * @param StorageRepositoryInterface $storageRepository
     * @param Config $config
     */
    public function __construct(
        StorageRepositoryInterface $storageRepository,
        Config $config
    ) {
        $this->storageRepository = $storageRepository;
        $this->config            = $config;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getGroupTitle()
    {
        return __('Timing');
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return self::UNIT_MILLISECOND;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return self::DIRECTION_UP;
    }

    /**
     * @return string[]
     */
    abstract protected function getActions();


    /**
     * @return bool|float|int|string
     */
    public function getValue()
    {
        $sum = 0;
        $qty = 0;

        foreach ($this->getActions() as $action) {
            $code = $this->config->getStorageCode($action, Config::AGGREGATION_SUM);
            $sum  += $this->storageRepository->getSnapshotValue($code, 0);

            $code = $this->config->getStorageCode($action, Config::AGGREGATION_QTY);
            $qty  += $this->storageRepository->getSnapshotValue($code, 0);
        }

        return $qty > 0 ? $sum / $qty : false;
    }

    /**
     * @return int|string
     */
    public function getConfidence()
    {
        $qty = 0;
        foreach ($this->getActions() as $action) {
            $code = $this->config->getStorageCode($action, Config::AGGREGATION_QTY);

            $qty += $this->storageRepository->getSnapshotValue($code, 0);
        }

        return $qty > 100 ? 100 : $qty;
    }
}
