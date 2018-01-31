<?php

namespace Strnoar\BQueueBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class QueueListenCommand extends ContainerAwareCommand
{
    /**
     * @var null|string
     */
    protected $parameters;

    /**
     * @param $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    protected function configure()
    {
        $this
            ->setName('worker:listen')
            ->setDescription('Listen a worker')
            ->addOption(
                'tube',
                null,
                InputOption::VALUE_OPTIONAL,
                'Which tube listen',
                $this->parameters['default']
            )
            ->addOption(
                'tries',
                null,
                InputOption::VALUE_OPTIONAL,
                'How many try',
                1
            )
            ->addOption(
                'infinite_retry',
                'i',
                InputOption::VALUE_NONE
            );
    }

    protected function execute(InputInterface $input, OutputInterface $putput)
    {
        $tube = $input->getOption('tube');
        $tube = null === $tube ? $this->parameters['default'] : $tube;

        if (true === $input->getOption('infinite_retry')) {
            $tries = -1;
        } else {
            $tries = $input->getOption('tries');
            $tries = null === $tries ? (int) $this->parameters['tries'] : (int) $tries;
        }

        $jobManager = $this
            ->getContainer()
            ->get('bqueuebundle.job_manager')
            ->execute($tube, $tries);
    }
}
