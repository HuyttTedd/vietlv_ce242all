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



namespace Mirasvit\StabilityError\Ui;

use Mirasvit\Stability\Ui\General\Toolbar\ButtonInterface;
use Mirasvit\StabilityError\Api\Data\ErrorInterface;
use Mirasvit\StabilityError\Api\Repository\ErrorRepositoryInterface;
use Mirasvit\StabilitySnapshot\Api\Service\SnapshotServiceInterface;

class ToolbarButton implements ButtonInterface
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
     * ToolbarButton constructor.
     * @param ErrorRepositoryInterface $errorRepository
     * @param SnapshotServiceInterface $snapshotService
     */
    public function __construct(
        ErrorRepositoryInterface $errorRepository,
        SnapshotServiceInterface $snapshotService
    ) {
        $this->errorRepository = $errorRepository;
        $this->snapshotService = $snapshotService;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getLabel()
    {
        return __('Errors');
    }

    /**
     * @return false|int
     */
    public function getExtra()
    {
        $snapshot = $this->snapshotService->getRunningSnapshot();
        if (!$snapshot) {
            return 0;
        }

        return $this->errorRepository->getCollection()
            ->addFieldToFilter(ErrorInterface::SNAPSHOT_ID, $snapshot->getId())
            ->getSize();
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return 50;
    }

    /**
     * @return string
     */
    public function getUiName()
    {
        return 'stability_error_listing';
    }
}
