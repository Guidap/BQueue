<?php

namespace Strnoar\BQueueBundle\Jobs;

abstract class Manager
{
    /**
     * @param array $payload
     * @return bool
     * @throws \Exception
     */
    protected function validate(Array $payload)
    {
        if (!array_key_exists('service', $payload) && !is_array($payload['parameters'])) {

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
}