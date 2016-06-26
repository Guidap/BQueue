<?php

namespace Strnoar\BQueueBundle\Jobs;

use Pheanstalk\Pheanstalk;
use Symfony\Bridge\Monolog\Logger;

class JobManager
{
    /**
     * @var Pheanstalk
     */
    private $pheanstalk;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var int
     */
    private $count = 1;

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
     * @param Jobs $command
     * @param string $tube
     * @return int
     */
    public function dispatch(Jobs $command, $tube = null)
    {
        $serialized = serialize($command);
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

        $command = unserialize($job->getData());


        if ($command instanceof Jobs && method_exists($command, 'handle') && is_callable([$command, 'handle'])) {

            try {

                call_user_func([$command, 'handle']);

            } catch(\Exception $e) {

                return $this->tries($tube, $tries, $index, $timeout, $job, $e);

            }

            return $this->pheanstalk->delete($job);
        }

        throw new \Exception('You cannot execute a non Jobs abstact class');
    }

    /**
     * @param $tube
     * @param $tries
     * @param $index
     * @param $timeout
     * @param $job
     * @param $e
     * @return bool|null
     * @throws \Exception
     */
    protected function tries($tube, $tries, $index, $timeout, $job, $e = null)
    {
        while ($this->count <= $tries) {
            $this->execute($tube, $tries, $index, $timeout);
            $this->count++;
        }

        $this->pheanstalk->delete($job);

        return $this->logger->alert($e->getMessage());
    }
}
