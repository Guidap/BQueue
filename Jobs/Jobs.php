<?php

namespace Strnoar\BQueueBundle\Jobs;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Class Jobs
 * @package Strnoar\BQueueBundle\Jobs
 */
abstract class Jobs implements JobsInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return $this
     */
    public function build()
    {
        return $this;
    }
}