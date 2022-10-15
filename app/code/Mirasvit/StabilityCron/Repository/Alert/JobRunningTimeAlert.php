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

class JobRunningTimeAlert implements AlertInterface
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
    private $errors = null;

    /**
     * JobRunningTimeAlert constructor.
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
        return __('Cron Job Running Time');
    }

    /**
     * @return int
     */
    public function getImportance()
    {
        return self::IMPORTANCE_NORMAL;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        $this->check();

        $list = [];

        if (count($this->errors) === 0) {
            $list[] = __('OK.');
        } else {
            $list[] = __('%1', implode(PHP_EOL, $this->errors));
        }

        return implode(' ', $list);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        $this->check();

        return count($this->errors) === 0 ? self::STATUS_SUCCESS : self::STATUS_WARING;
    }

    private function check()
    {
        if ($this->errors !== null) {
            return;
        }

        $collection = $this->scheduleCollectionFactory->create();

        $collection
            ->addFieldToFilter('status', 'running')
            ->setOrder('scheduled_at', 'desc')
            ->setPageSize(10);

        $this->errors = [];
        foreach ($collection as $job) {
            $jobTimestamp = strtotime($job->getExecutedAt()); //in store timezone
            $timestamp    = strtotime($this->dateTime->gmtDate());  //in store timezone

            if ($timestamp - $jobTimestamp > 10 * 60) {
                $this->errors[] = __(
                    'Job `%1` started at %2 and still running.',
                    $job->getJobCode(),
                    $this->dateTime->date('M d, H:i:s', $jobTimestamp)
                );
            }
        }
    }
}
