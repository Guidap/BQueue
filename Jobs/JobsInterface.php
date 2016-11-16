<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 24/06/16
 * Time: 16:13
 */

namespace Strnoar\BQueueBundle\Jobs;


interface JobsInterface
{
    /**
     * @param array $parameters
     * @return mixed
     */
    public function handle(array $parameters);
}