<?php

namespace Strnoar\BQueueBundle\Jobs;

use Pheanstalk\PheanstalkInterface;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class JobManager extends Manager
{
    use ContainerAwareTrait;

    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var int
     */
    protected $count = 1;

    /**
     * JobManager constructor.
     * @param Logger $logger
     * @param $parameters
     */
    public function __construct(Logger $logger, $parameters)
    {
        $this->parameters = $parameters;
        $this->logger = $logger;
        $this->init();
    }


    /**
     * @param $service
     * @param array $payload
     * @param null $tube
     * @return int
     */
    public function dispatch($service, array $payload, $tube = null)
    {
        if ($this->isBeanstald) {
            $serialized = serialize(['service' => $service, 'parameters' => $payload]);
            $tube = null === $tube ? $this->parameters['default'] : $tube;

            return $this->pheanstalk->useTube($tube)->put($serialized);
        }

        return $this->sync($service, $payload);
    }


    /**
     * @param null $tube
     * @param int $tries
     * @param int $index
     * @param int $timeout
     * @return $this|string|void
     * @throws \Exception
     */
    public function execute($tube = null, $tries = 1, $index = 0, $timeout = 0)
    {
        $tube = null === $tube ? $this->parameters['default'] : $tube;
        $job = $this->pheanstalk->watch($tube)->reserve($timeout);

        if (false === $job) {
            return;
        }

        $payload = unserialize($job->getData());

        if (!$this->validate($payload)) {
            throw new \Exception('The payload value is not valid, please verify the service and the parameters');
        }

        try {
            call_user_func(
                [$this->container->get($payload['service']), 'handle'],
                $payload['parameters']
            );
        } catch (\Throwable $e) {
            if ($tries === -1) {
                $this->logger->alert('JOB ERROR: release', [
                    'exception' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
                $this->pheanstalk->release($job, PheanstalkInterface::DEFAULT_PRIORITY, $this->parameters['infinite_retry_delay']);
                return;
            }
            return $this->tries($tube, $tries, $index, $timeout, $job, $e);
        }

        return $this->pheanstalk->delete($job);
    }

    /**
     * @param $service
     * @param array $payload
     */
    private function sync($service, array $payload)
    {
        if ($this->container->has($service)) {
            call_user_func(
                [$this->container->get($service), 'handle'],
                $payload
            );
        }
    }
}
