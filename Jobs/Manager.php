<?php

namespace Strnoar\BQueueBundle\Jobs;

use Pheanstalk\Pheanstalk;

abstract class Manager
{
    /**
     * @var Pheanstalk
     */
    protected $pheanstalk;
    /**
     * @var bool
     */
    protected $isBeanstald;
    /**
     * @var array
     */
    protected $parameters;

    /**
     * @param array $payload
     * @return bool
     * @throws \Exception
     */
    protected function validate(array $payload)
    {
        if (!array_key_exists('service', $payload) && !is_array($payload)) {
            return false;
        }

        return ($this->container->get($payload['service']) instanceof JobsInterface);
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

    protected function init()
    {
        $this->isBeanstald = ($this->parameters['adapter'] == 'beanstalkd');
        if ($this->isBeanstald) {
            $this->pheanstalk = new Pheanstalk($this->parameters['host'], $this->parameters['port']);
        }
    }
}