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



namespace Mirasvit\StabilityCron\Repository\EnvironmentData;

use Magento\Cron\Model\ConfigInterface as CronConfigInterface;
use Mirasvit\StabilitySnapshot\Api\Data\Snapshot\EnvironmentDataInterface;

class CronData implements EnvironmentDataInterface
{
    /**
     * @var CronConfigInterface
     */
    private $cronConfig;

    /**
     * CronData constructor.
     * @param CronConfigInterface $cronConfig
     */
    public function __construct(
        CronConfigInterface $cronConfig
    ) {
        $this->cronConfig = $cronConfig;
    }


    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Cron Jobs';
    }

    /**
     * @return array
     */
    public function capture()
    {
        $data = [];

        foreach ($this->cronConfig->getJobs() as $name => $job) {
            $data[$name] = [
                'label' => $name,
                'value' => isset($job['schedule']) ? $job['schedule'] : '',
            ];
        }

        return $data;
    }
}
