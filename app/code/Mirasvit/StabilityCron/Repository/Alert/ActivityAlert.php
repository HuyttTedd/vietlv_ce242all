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



namespace Mirasvit\StabilityCron\Repository\Alert;

use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory as ScheduleCollectionFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mirasvit\StabilityAlert\Api\Data\AlertInterface;

class ActivityAlert implements AlertInterface
{
    /**
     * @var ScheduleCollectionFactory
     */
    private $scheduleCollectionFactory;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var null
     */
    private $isRunning         = null;

    /**
     * @var null
     */
    private $lastExecutionTime = null;

    /**
     * ActivityAlert constructor.
     * @param ScheduleCollectionFactory $scheduleCollectionFactory
     * @param DateTime $dateTime
     */
    public function __construct(
        ScheduleCollectionFactory $scheduleCollectionFactory,
        DateTime $dateTime
    ) {
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->dateTime                  = $dateTime;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getName()
    {
        return __('Cron Job Status');
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
     */
    public function getDescription()
    {
        $this->check();

        $list = [];

        if (!$this->isRunning) {
            $list[] = __('Cron job is not working.');
        }

        $list[] = $this->lastExecutionTime
            ? __('Last execution time %1.', $this->dateTime->date('M d, H:i:s', $this->lastExecutionTime))
            : __('All jobs were registered.');

        return implode(' ', $list);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        $this->check();

        return $this->isRunning ? self::STATUS_SUCCESS : self::STATUS_ERROR;
    }

    private function check()
    {
        if ($this->isRunning !== null) {
            return;
        }

        $collection = $this->scheduleCollectionFactory->create();

        $collection
            ->addFieldToFilter('status', 'success')
            ->setOrder('scheduled_at', 'desc')
            ->setPageSize(1);

        /** @var \Magento\Cron\Model\Schedule $job */
        $job = $collection->getFirstItem();
        if (!$job->getId()) {
            $this->isRunning = false;

            return;
        }

        $jobTimestamp = strtotime($job->getExecutedAt()); //in store timezone
        $timestamp    = strtotime($this->dateTime->gmtDate());  //in store timezone

        $this->lastExecutionTime = $jobTimestamp;

        if (abs($timestamp - $jobTimestamp) > 6 * 60 * 60) {
            $this->isRunning = false;
        } else {
            $this->isRunning = true;
        }
    }
}
