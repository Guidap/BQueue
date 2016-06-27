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
            );
    }

    protected function execute(InputInterface $input, OutputInterface $putput)
    {
        $tube = is_null($input->getOption('tube')) ? $this->parameters['default'] :  $input->getOption('tube');
        $tries = is_null($input->getOption('tries')) ? $this->parameters['tries'] :  $input->getOption('tries');

        $jobManager = $this->getContainer()->get('bqueuebundle.job_manager');

        $jobManager->execute($tube, $tries);
    }
}