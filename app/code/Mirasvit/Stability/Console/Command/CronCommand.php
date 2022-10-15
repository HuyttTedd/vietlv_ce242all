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



namespace Mirasvit\Stability\Console\Command;

use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronCommand extends Command
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * CronCommand constructor.
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('mirasvit:stability:cron')
            ->setDescription('Health Monitoring Suite');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobs = [
            \Mirasvit\Stability\Cron\StorageCron::class,
            \Mirasvit\StabilitySnapshot\Cron\SnapshotCron::class,
        ];
        foreach ($jobs as $job) {
            $output->write("$job.... ");

            $obj = $this->objectManager->create($job);
            $obj->execute();

            $output->writeln("completed");
        }
    }
}
