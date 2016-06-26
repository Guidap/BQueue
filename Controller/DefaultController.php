<?php

namespace Strnoar\BQueueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="default.index")
     */
    public function indexAction()
    {
        $job = $this->get("test.testjob")->prepare(new Test('Salut maman !'))->build();

        $c = 1;

        while ($c <= 10) {

            $this->get('bqueuebundle.job_manager')->dispatch($job);

            $c++;
        }

        return $this->render('StrnoarBQueueBundle::default/index.html.twig');
    }
}