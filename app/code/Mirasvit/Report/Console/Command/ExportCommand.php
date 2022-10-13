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
 * @package   mirasvit/module-report
 * @version   1.3.110
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */




namespace Mirasvit\Report\Console\Command;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\StateFactory;
use Magento\Framework\Filesystem;
use Mirasvit\Core\Service\CompatibilityService;
use Mirasvit\Report\Api\Data\ReportInterface;
use Mirasvit\Report\Api\Service\DateServiceInterface;
use Mirasvit\Report\Api\Service\IntervalInterface;
use Mirasvit\Report\Model\Export\ConvertToCsv;
use Mirasvit\Report\Model\Export\ConvertToXml;
use Mirasvit\Report\Repository\ReportRepository;
use Mirasvit\ReportApi\Api\RequestBuilderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ExportCommand extends Command
{
    private $appStateFactory;

    private $repository;

    private $requestBuilder;

    private $convertToXml;

    private $convertToCsv;

    private $filesystem;

    public function __construct(
        StateFactory $appStateFactory,
        ReportRepository $repository,
        RequestBuilderInterface $requestBuilder,
        Filesystem $filesystem,
        ConvertToCsv $convertToCsv,
        ConvertToXml $convertToXml
    ) {
        $this->appStateFactory = $appStateFactory;
        $this->repository      = $repository;
        $this->requestBuilder  = $requestBuilder;
        $this->filesystem      = $filesystem;
        $this->convertToCsv    = $convertToCsv;
        $this->convertToXml    = $convertToXml;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:report:export')
            ->setDescription('Export report');

        $this->addOption('type', null, InputOption::VALUE_REQUIRED, 'Export type (csv, xml)');
        $this->addOption(
            'identifier',
            null,
            InputOption::VALUE_REQUIRED,
            'Report identifier (To see the list with all available reports run <comment>mirasvit:report:export --report-list</comment> command)'
        );
        $this->addOption(
            'interval',
            null,
            InputOption::VALUE_REQUIRED,
            'Specify report date interval (run <comment>mirasvit:report:export --interval-list</comment> to see all available intrvals). Avoid this option for lifetime reports'
        );

        $this->addOption('report-list', null, InputOption::VALUE_NONE, 'Display all reports identifiers');
        $this->addOption('interval-list', null, InputOption::VALUE_NONE, 'Display all available date intervals');

        parent::configure();
    }

    /**
     * @SuppressWarnings(PHPMD)
     *
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appStateFactory->create()->setAreaCode('frontend');
        } catch (\Exception $e) {
        }

        /** @var DateServiceInterface $dateService */
        $dateService = CompatibilityService::getObjectManager()->get(DateServiceInterface::class);

        if ($input->getOption('report-list')) {
            $reports = $this->repository->getList();

            $output->writeln("<info>Available Reports:</info>");
            foreach ($reports as $report) {
                $name = (string)$report->getName();
                $identifier = (string)$report->getIdentifier();
                $tab = str_repeat(" ", strlen($identifier) >= 30 ? 0 : 30 - strlen($identifier));

                $output->writeln("<comment>{$identifier}</comment> {$tab}- {$name}");
            }

            return;
        }

        if ($input->getOption('interval-list')) {
            $intervals = $dateService->getIntervals();

            $output->writeln("<info>Available Intervals:</info>");
            foreach ($intervals as $code => $label) {
                $tab = str_repeat(" ", 12 - strlen($code));

                $output->writeln("<comment>{$code}</comment>{$tab} - $label");
            }

            return;
        }

        $type = $input->getOption('type');

        if (!$type || ($type !== 'csv' && $type !== 'xml')) {
            $output->writeln("<error>Correct export type should be specified</error>");
            $output->writeln("<info>Allowed types: csv, xml</info>");

            return;
        }

        $identifier = $input->getOption('identifier');

        if (!$identifier) {
            $output->writeln("<error>Report identifier should be specified.</error>");
            $output->writeln("<info>To see the list with all available reports run <comment>mirasvit:report:export --report-list</comment> command</info>");

            return;
        }

        $interval = $input->getOption('interval') ?: 'lifetime';

        $intervalCodes = array_keys($dateService->getIntervals());

        if (!in_array($interval, $intervalCodes)) {
            $output->writeln("<error>'{$interval}' is not in the list of available date intervals</error>");
            $output->writeln("<info>To see the list with all available date intervals run <comment>mirasvit:report:export --interval-list</comment> command</info>");
            $output->writeln("Avoid this option for lifetime reports");

            return;
        }

        $report = $this->repository->get($identifier);

        if (!$report) {
            $output->writeln("<error>Report with identifier {$identifier} does not exists.</error>");
            $output->writeln("<info>To see the list with all available reports run <comment>mirasvit:report:export --report-list</comment> command</info>");

            return;
        }

        $output->writeln("Building report...");

        $response = $this->buildReport($report, $dateService->getInterval($interval));

        $content = $type === 'xml'
            ? $this->convertToXml->getXmlFile($response)
            : $this->convertToCsv->getCsvFile($response);

        $path = $content['value'];

        $copied = false;

        $varDir = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);

        if ($varDir->isExist($content['value'])) {
            $copyPath = 'export/report_' . $identifier . '_' . $interval . '_' . date('j-m-Y_h-i-s') . '.' . $type;

            $copied = $varDir->copyFile($path, $copyPath);
            $varDir->delete($path);
        }

        if ($copied) {
            $rootPath = $varDir->getAbsolutePath() . $copyPath;
            $output->writeln("Report saved in the file <comment>{$rootPath}</comment>");
        }
    }

    /**
     * @SuppressWarnings(PHPMD)
     *
     * @param ReportInterface $report
     * @param IntervalInterface $interval
     *
     * @return \Mirasvit\ReportApi\Api\RequestInterface
     */
    private function buildReport($report, $interval)
    {
        $request = $this->requestBuilder->create();

        $request->setTable($report->getTable())->setDimensions($report->getDimensions());

        $hasDateDimension = false;

        foreach ($report->getDimensions() as $c) {
            $request->addColumn($c);

            if (!$hasDateDimension && strpos($c, "_at") !== false) {
                $hasDateDimension = true;
            }
        }

        foreach ($report->getColumns() as $c) {
            $request->addColumn($c);
        }

        foreach ($report->getInternalFilters() as $filter) {
            if ($filter['conditionType'] == 'like') {
                $filter['value'] = '%' . $filter['value'] . '%';
            }

            if(!$hasDateDimension && strpos($filter['column'], "_at") !== false) {
                $hasDateDimension = true;
            }

            $request->addFilter($filter['column'], $filter['value'], $filter['conditionType']);
        }

        foreach($report->getPrimaryFilters() as $filter) {
            if(!$hasDateDimension && strpos($filter, "_at") !== false) {
                $hasDateDimension = true;
                $dimensionFilter  = $filter;
            }
        }

        $filterColumn = isset($dimensionFilter) ? $dimensionFilter : $report->getTable() . '|created_at';

        if ($hasDateDimension) {
            $request->addFilter($filterColumn, $interval->getFrom()->toString('Y-MM-dd HH:mm:ss'), 'gteq', 'A')
                ->addFilter($filterColumn, $interval->getTo()->toString('Y-MM-dd HH:mm:ss'), 'lteq', 'A');
        }

        return $request;
    }
}
