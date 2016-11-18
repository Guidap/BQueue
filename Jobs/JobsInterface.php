<?php

namespace Strnoar\BQueueBundle\Jobs;

interface JobsInterface
{
    /**
     * @param array $parameters
     * @return mixed
     */
    public function handle(array $parameters);
}