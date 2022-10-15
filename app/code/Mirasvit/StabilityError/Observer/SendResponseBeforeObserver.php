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



namespace Mirasvit\StabilityError\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mirasvit\Stability\Api\Repository\StorageRepositoryInterface;
use Mirasvit\StabilityError\Model\Config;

class SendResponseBeforeObserver implements ObserverInterface
{
    /**
     * @var StorageRepositoryInterface
     */
    private $storageRepository;

    /**
     * SendResponseBeforeObserver constructor.
     * @param StorageRepositoryInterface $storageRepository
     */
    public function __construct(
        StorageRepositoryInterface $storageRepository
    ) {
        $this->storageRepository = $storageRepository;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $code = Config::STORAGE_REQUEST_COUNTER_CODE;
        $this->storageRepository->setSnapshotValue(
            $code,
            $this->storageRepository->getSnapshotValue($code, 0) + 1
        );
    }
}
