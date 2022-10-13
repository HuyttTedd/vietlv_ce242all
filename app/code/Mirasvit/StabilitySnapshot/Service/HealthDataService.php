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



namespace Mirasvit\StabilitySnapshot\Service;

use Magento\Framework\ObjectManagerInterface;
use Mirasvit\StabilitySnapshot\Api\Data\Snapshot\HealthDataInterface;
use Mirasvit\StabilitySnapshot\Api\Data\SnapshotInterface;
use Mirasvit\StabilitySnapshot\Api\Repository\SnapshotRepositoryInterface;

class HealthDataService
{
    /**
     * @var SnapshotRepositoryInterface
     */
    private $snapshotRepository;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    const DATA_VALUE      = 'value';
    const DATA_CONFIDENCE = 'confidence';

    /**
     * HealthDataService constructor.
     * @param SnapshotRepositoryInterface $snapshotRepository
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        SnapshotRepositoryInterface $snapshotRepository,
        ObjectManagerInterface $objectManager
    ) {
        $this->snapshotRepository = $snapshotRepository;
        $this->objectManager      = $objectManager;
    }

    /**
     * @return array
     */
    public function capture()
    {
        $data = [];

        foreach ($this->snapshotRepository->getHealthDataPool() as $healthData) {
            $data[get_class($healthData)] = [
                self::DATA_VALUE      => $healthData->getValue(),
                self::DATA_CONFIDENCE => $healthData->getConfidence(),
            ];
        }

        return $data;
    }

    /**
     * @param SnapshotInterface $closed
     * @param SnapshotInterface $running
     * @return array
     */
    public function compare(SnapshotInterface $closed, SnapshotInterface $running)
    {
        $newData = $this->unpackData($running->getHealthData());
        $oldData = $this->unpackData($closed->getHealthData());

        if (!$oldData) {
            return $this->objToArray($newData);
        }

        foreach ($newData as $groupKey => $group) {
            foreach ($group as $metricKey => $metric) {
                $newValue = $metric[self::DATA_VALUE];
                if (isset($oldData[$groupKey]) && isset($oldData[$groupKey][$metricKey])) {
                    $oldValue = $oldData[$groupKey][$metricKey][self::DATA_VALUE];

                    if ($newValue && $oldValue) {
                        $diff = (abs($newValue) - abs($oldValue)) / abs($newValue);
                        $diff = $diff * 100;

                        $newData[$groupKey][$metricKey]['diff'] = $diff;
                    }
                }
            }
        }
        $newData = $this->objToArray($newData);

        return $newData;
    }


    /**
     * @param array $healthData
     * @return array
     */
    private function unpackData($healthData)
    {
        $result = [];
        foreach ($healthData as $class => $data) {
            /** @var HealthDataInterface $instance */
            $instance = $this->objectManager->create($class);

            $result[(string)$instance->getGroupTitle()][(string)$instance->getTitle()] = [
                'label'      => (string)$instance->getTitle(),
                'unit'       => $instance->getUnit(),
                'direction'  => $instance->getDirection(),
                'value'      => $data[self::DATA_VALUE],
                'confidence' => $data[self::DATA_CONFIDENCE],
            ];
        }

        return $result;
    }

    /**
     * @param array $data
     * @return array
     */
    private function objToArray($data)
    {
        foreach ($data as $group => $values) {
            $data[$group] = [
                'label'  => $group,
                'values' => array_values($values),
            ];
        }

        return array_values($data);
    }
}
