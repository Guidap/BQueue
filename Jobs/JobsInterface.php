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
     * @return mixed
     */
    public function handle();
}