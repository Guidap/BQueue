<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 24/06/16
 * Time: 18:50
 */

namespace Strnoar\BQueueBundle\Controller;


class Test {
    public $txt;

    /**
     * Test constructor.
     * @param $txt
     */
    public function __construct($txt)
    {
        $this->txt = $txt;
    }

    public function sayHi()
    {
        return print $this->txt . ' fucking mother fucker';
    }

}