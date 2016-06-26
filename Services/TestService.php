<?php

namespace Strnoar\BQueueBundle\Services;

use Strnoar\BQueueBundle\Controller\Test;
use Strnoar\BQueueBundle\Jobs\Jobs;

class TestService extends Jobs
{
    /**
     * @var Int
     */
    public $test;
    /**
     * @var Logger
     */
    public $logger;

    /**
     * @param $id
     * @return $this
     */
    public function prepare($test)
    {
        $this->test = $test;

        return $this;
    }

    /**
     * @return mixed
     */
    public function handle()
    {

        return $this->test->sayHi();

        $this->logger->info('ID : ' . $this->test);
    }

    /**
     * @param \Symfony\Bridge\Monolog\Logger $logger
     */
    public function setLogger(\Symfony\Bridge\Monolog\Logger $logger)
    {
        $this->logger = $logger;
    }
}