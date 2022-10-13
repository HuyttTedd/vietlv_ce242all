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



namespace Mirasvit\StabilityError\Repository\HealthData;

use Mirasvit\Stability\Api\Repository\StorageRepositoryInterface;
use Mirasvit\StabilityError\Api\Data\ErrorInterface;
use Mirasvit\StabilityError\Api\Repository\ErrorRepositoryInterface;
use Mirasvit\StabilityError\Model\Config;
use Mirasvit\StabilitySnapshot\Api\Data\Snapshot\HealthDataInterface;
use Mirasvit\StabilitySnapshot\Api\Service\SnapshotServiceInterface;

class ServerErrorRate implements HealthDataInterface
{
    /**
     * @var ErrorRepositoryInterface
     */
    private $errorRepository;

    /**
     * @var SnapshotServiceInterface
     */
    private $snapshotService;

    /**
     * @var StorageRepositoryInterface
     */
    private $storageRepository;

    /**
     * ServerErrorRate constructor.
     * @param ErrorRepositoryInterface $errorRepository
     * @param SnapshotServiceInterface $snapshotService
     * @param StorageRepositoryInterface $storageRepository
     */
    public function __construct(
        ErrorRepositoryInterface $errorRepository,
        SnapshotServiceInterface $snapshotService,
        StorageRepositoryInterface $storageRepository
    ) {
        $this->errorRepository   = $errorRepository;
        $this->snapshotService   = $snapshotService;
        $this->storageRepository = $storageRepository;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getGroupTitle()
    {
        return __('Errors');
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTitle()
    {
        return __('Server Error Rate');
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return self::DIRECTION_UP;
    }

    /**
     * @return float|int
     */
    public function getConfidence()
    {
        $requests = $this->storageRepository->getSnapshotValue(Config::STORAGE_REQUEST_COUNTER_CODE, 0);

        return $requests > 1000 ? 100 : $requests / 10;
    }

    /**
     * @return float|int|string|null
     */
    public function getValue()
    {
        $snapshot = $this->snapshotService->getRunningSnapshot();

        $errors = $this->errorRepository->getCollection()
            ->addFieldToFilter(ErrorInterface::SNAPSHOT_ID, $snapshot->getId())
            ->getSize();

        $requests = $this->storageRepository->getSnapshotValue(Config::STORAGE_REQUEST_COUNTER_CODE, 0);

        return $requests > 0 ? $errors / $requests * 1000 : null;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return self::UNIT_PER_1000;
    }
}
