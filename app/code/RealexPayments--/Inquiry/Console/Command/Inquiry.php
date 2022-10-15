<?php

declare(strict_types=1);

namespace RealexPayments\Inquiry\Console\Command;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use RealexPayments\Inquiry\Model\Command\Validator;
use RealexPayments\Inquiry\Model\Inquiry\Handler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Inquiry.
 */
class Inquiry extends Command
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var Handler
     */
    protected $inquiryHandler;

    /**
     * @var State
     */
    protected $state;

    /**
     * @param Validator $validator
     * @param Handler $inquiryHandler
     * @param State $state
     * @param string|null $name
     */
    public function __construct(
        Validator $validator,
        Handler $inquiryHandler,
        State $state,
        string $name = null
    ) {
        $this->validator        = $validator;
        $this->inquiryHandler   = $inquiryHandler;
        $this->state            = $state;

        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('tls:global-pay:inquiry')
            ->setDescription('RealexPayments Inquiry');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->setAreaIfNotDefined();
            $this->validator->execute();
        } catch (Exception $e) {
            $output->writeln(sprintf('<info>%s</info>', $e->getMessage()));

            return 0;
        }

        $output->writeln('<info>Start reconciling orders!</info>');

        try {
            $this->inquiryHandler->handleInquiry();
        } catch (Exception $e) {
            $output->writeln(sprintf('<info>Reconcile orders command ends with error. %s</info>', $e->getMessage()));

            return 1;
        }

        $output->writeln('<info>Reconcile orders successfully!</info>');

        return 0;
    }

    /**
     * @throws LocalizedException
     */
    protected function setAreaIfNotDefined()
    {
        try {
            $this->state->getAreaCode();
        } catch (LocalizedException $e) {
            $this->state->setAreaCode(Area::AREA_CRONTAB);
        }
    }
}
