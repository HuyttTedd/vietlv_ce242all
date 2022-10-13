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
use Mirasvit\StabilityAlert\Api\Data\AlertInterface;

class JobErrorAlert implements AlertInterface
{
    /**
     * @var ScheduleCollectionFactory
     */
    private $scheduleCollectionFactory;

    /**
     * @var null
     */
    private $errors = null;

    /**
     * JobErrorAlert constructor.
     * @param ScheduleCollectionFactory $scheduleCollectionFactory
     */
    public function __construct(
        ScheduleCollectionFactory $scheduleCollectionFactory
    ) {
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getName()
    {
        return __('Cron Job Errors');
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

        if (count($this->errors) === 0) {
            $list[] = __('All OK.');
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
            ->addFieldToFilter('status', 'error')
            ->setOrder('scheduled_at', 'desc')
            ->setPageSize(10);

        $this->errors = [];
        foreach ($collection as $job) {
            $this->errors[] = $job->getJobCode() . ': ' . $job->getMessages();
        }
    }
}
