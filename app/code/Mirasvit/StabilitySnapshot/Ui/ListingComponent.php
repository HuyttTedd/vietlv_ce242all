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



namespace Mirasvit\StabilitySnapshot\Ui;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\AbstractComponent;
use Mirasvit\StabilitySnapshot\Api\Data\SnapshotInterface;
use Mirasvit\StabilitySnapshot\Repository\SnapshotRepository;
use Mirasvit\StabilitySnapshot\Service\HealthDataService;
use Mirasvit\StabilitySnapshot\Service\SnapshotService;

class ListingComponent extends AbstractComponent
{
    /**
     * @var SnapshotRepository
     */
    private $snapshotRepository;

    /**
     * @var SnapshotService
     */
    private $snapshotService;

    /**
     * @var HealthDataService
     */
    private $healthDataService;

    /**
     * ListingComponent constructor.
     * @param SnapshotRepository $snapshotRepository
     * @param SnapshotService $snapshotService
     * @param HealthDataService $healthDataService
     * @param ContextInterface $context
     * @param array $components
     * @param array $data
     */
    public function __construct(
        SnapshotRepository $snapshotRepository,
        SnapshotService $snapshotService,
        HealthDataService $healthDataService,
        ContextInterface $context,
        $components = [],
        array $data = []
    ) {
        $this->snapshotRepository = $snapshotRepository;
        $this->snapshotService    = $snapshotService;
        $this->healthDataService  = $healthDataService;

        parent::__construct($context, $components, $data);
    }

    /**
     * @return string
     */
    public function getComponentName()
    {
        return 'snapshot_list';
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $config = $this->getData('config');

        $config['snapshots'] = [];

        $prevSnapshot = null;

        $collection = $this->snapshotRepository->getCollection();
        $collection->setOrder(SnapshotInterface::CREATED_AT, 'desc')
            ->setPageSize(1000)
            ->setCurPage(1);

        /** @var SnapshotInterface $snapshot */
        foreach ($collection as $snapshot) {
            $data = [
                SnapshotInterface::ID          => $snapshot->getId(),
                SnapshotInterface::STATUS      => $snapshot->getStatus(),
                SnapshotInterface::NOTE        => $snapshot->getNote(),
                SnapshotInterface::CREATED_AT  => $snapshot->getCreatedAt(),
                SnapshotInterface::CLOSED_AT   => $snapshot->getClosedAt(),
                SnapshotInterface::HEALTH_DATA => $this->healthDataService->compare(
                    $this->snapshotRepository->create(),
                    $snapshot
                ),
            ];

            if ($prevSnapshot) {
                $prevIdx         = count($config['snapshots']) - 1;
                $environmentData = $this->snapshotService->compare($snapshot, $prevSnapshot);
                if (!$environmentData) {
                    $environmentData = false;
                }
                $config['snapshots'][$prevIdx][SnapshotInterface::ENVIRONMENT_DATA] = $environmentData;
                $config['snapshots'][$prevIdx][SnapshotInterface::HEALTH_DATA]      = $this->healthDataService->compare(
                    $snapshot,
                    $prevSnapshot
                );
            }

            $prevSnapshot = $snapshot;

            $config['snapshots'][] = $data;
        }
        //
        //        echo '<pre>';
        //        print_R($config);
        //        die();

        $this->setData('config', $config);

        parent::prepare();
    }
}
