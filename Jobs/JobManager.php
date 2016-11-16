<?php

namespace Strnoar\BQueueBundle\Jobs;

use Pheanstalk\Pheanstalk;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class JobManager extends Manager
{
    use ContainerAwareTrait;

    /**
     * @var Pheanstalk
     */
    protected $pheanstalk;
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
        $this->pheanstalk = new Pheanstalk($parameters['host'], $parameters['port']);
        $this->logger = $logger;
    }


    /**
     * @param $service
     * @param array $payload
     * @param null $tube
     * @return int
     */
    public function dispatch($service, Array $payload, $tube = null)
    {
        $serialized = serialize(array_merge(['service' => $service], $payload));
        $tube = is_null($tube) ? $this->parameters['default'] : $tube;

        return $this->pheanstalk->useTube($tube)->put($serialized);
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
        $tube = is_null($tube) ? $this->parameters['default'] : $tube;
        $job = $this->pheanstalk->watch($tube)->reserve($timeout);

        if (false === $job) {
            return;
        }

        $payload = unserialize($job->getData());

        if ($this->validate($payload)) {
            try {
                call_user_func(
                    [$this->container->get($payload['service']), 'handle'],
                    $payload['parameters']
                );
            } catch(\Exception $e) {
                return $this->tries($tube, $tries, $index, $timeout, $job, $e);
            }

            return $this->pheanstalk->delete($job);
        }

        throw new \Exception('The payload value is not valid, please verify the service and the parameters');
    }
}
