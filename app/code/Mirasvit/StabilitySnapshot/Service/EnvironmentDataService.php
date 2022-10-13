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

use Mirasvit\Stability\Api\Data\SnapshotDataSectionInterface;
use Mirasvit\StabilitySnapshot\Api\Data\SnapshotInterface;
use Mirasvit\StabilitySnapshot\Api\Repository\SnapshotRepositoryInterface;

class EnvironmentDataService
{
    /**
     * @var SnapshotRepositoryInterface
     */
    private $snapshotRepository;

    /**
     * EnvironmentDataService constructor.
     * @param SnapshotRepositoryInterface $snapshotRepository
     */
    public function __construct(
        SnapshotRepositoryInterface $snapshotRepository
    ) {
        $this->snapshotRepository = $snapshotRepository;
    }

    /**
     * @return array
     */
    public function capture()
    {
        $data = [];

        foreach ($this->snapshotRepository->getEnvironmentDataPool() as $envData) {
            $data[$envData->getTitle()] = $envData->capture();
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
        $diff = [];

        $newData = $running->getEnvironmentData();
        $oldData = $closed->getEnvironmentData();

        if (!$newData || !$oldData) {
            return $diff;
        }

        foreach ($newData as $sectionKey => $section) {
            foreach ($section as $paramKey => $param) {
                $newValue = $newData[$sectionKey][$paramKey]['value'];
                $label    = isset($newData[$sectionKey][$paramKey]['label'])
                    ? '<span>' . $newData[$sectionKey][$paramKey]['label'] . '</span>'
                    : '';
                $value    = null;
                if (isset($oldData[$sectionKey][$paramKey])) {
                    $oldValue = $oldData[$sectionKey][$paramKey]['value'];
                    if ($newValue !== $oldValue) {
                        // changed
                        $value = $this->markChanged($label . $newValue . $this->markRemoved($oldValue)
                            . '<em>changed</em>');
                    }
                } else {
                    // added
                    $value = $this->markAdded($label . $newValue . '<em>added</em>');
                }

                if ($value) {
                    $diff[$sectionKey][] = $value;
                }
            }
        }

        foreach ($oldData as $sectionKey => $section) {
            foreach ($section as $paramKey => $param) {
                $newValue = $oldData[$sectionKey][$paramKey]['value'];
                $label    = isset($oldData[$sectionKey][$paramKey]['label'])
                    ? '<span>' . $oldData[$sectionKey][$paramKey]['label'] . '</span>'
                    : '';
                $value    = null;
                if (!isset($newData[$sectionKey][$paramKey])) {
                    // removed
                    $value = $this->markRemoved($label . $newValue . '<em>removed</em>');
                }

                if ($value) {
                    $diff[$sectionKey][] = $value;
                }
            }
        }

        return $diff;
    }

    /**
     * @param string $value
     * @return string
     */
    private function markAdded($value)
    {
        return "<strong>$value</strong>";
    }

    /**
     * @param string $value
     * @return string
     */
    private function markChanged($value)
    {
        return "<i>$value</i>";
    }

    /**
     * @param string $value
     * @return string
     */
    private function markRemoved($value)
    {
        return "<s>$value</s>";
    }
}
