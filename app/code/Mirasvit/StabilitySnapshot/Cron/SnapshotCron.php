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



namespace Mirasvit\StabilitySnapshot\Cron;

use Mirasvit\StabilitySnapshot\Api\Data\SnapshotInterface;
use Mirasvit\StabilitySnapshot\Api\Repository\SnapshotRepositoryInterface;
use Mirasvit\StabilitySnapshot\Service\SnapshotService;

class SnapshotCron
{
    /**
     * @var SnapshotService
     */
    private $snapshotService;

    /**
     * @var SnapshotRepositoryInterface
     */
    private $snapshotRepository;

    /**
     * SnapshotCron constructor.
     * @param SnapshotService $snapshotService
     * @param SnapshotRepositoryInterface $snapshotRepository
     */
    public function __construct(
        SnapshotService $snapshotService,
        SnapshotRepositoryInterface $snapshotRepository
    ) {
        $this->snapshotService    = $snapshotService;
        $this->snapshotRepository = $snapshotRepository;
    }

    public function execute()
    {
        $newSnapshot = $this->snapshotService->makeSnapshot();

        $runningSnapshot = $this->snapshotService->getRunningSnapshot();

        //snapshot is empty (manually created)
        if (!$runningSnapshot->getEnvironmentData()) {
            $runningSnapshot->setEnvironmentData($newSnapshot->getEnvironmentData());
        }

        if ($runningSnapshot->getEnvironmentHash() != $newSnapshot->getEnvironmentHash()) {
            $runningSnapshot = $this->snapshotService->closeSnapshot($runningSnapshot);
            $this->snapshotRepository->save($runningSnapshot);

            $runningSnapshot = $this->snapshotRepository->save($newSnapshot);
        }

        $runningSnapshot->setHealthData($newSnapshot->getHealthData());

        $this->snapshotRepository->save($runningSnapshot);
    }
}
