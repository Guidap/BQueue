
# BQueue Bundle

BQueue is a Beanstalkd ready queue system for Symfony >= 2.8. Install, configure and use it !

#### Installation

```sh
$ composer require strnoar/bqueuebundle
```

Add the following line to your AppKernel.php:
```sh
new Strnoar\BQueueBundle\StrnoarBQueueBundle(),
```

#### Configuration

Add the following lines to your config.yml:
```sh
strnoar_b_queue:
    host: 127.0.0.1
    port: 11300
    default: default    # the default tube name
    tries: 1            # the number of time the manager try a worker who failed 
```

#### Use

You can access to the worker manager by the container with the id: 'bqueuebundle.job_manager'.

Here the dispatcher:


```sh
$this->get('bqueuebundle.job_manager')
            ->dispatch($this->get('my_bundle.my_worker_service')
            ->build());
```

if you need to inject dependencies in your worker you can use a method who return the worker instance ($this):

```sh
// MyBundle/Workers/MyWorker.php

public function setDependencies(Swift_Mailer $mailer)
{
    $this->mailer = $mailer;

    return $this;
}
```

And the dispatcher:

```sh
$this->get('bqueuebundle.job_manager')
            ->dispatch($this->get('my_bundle.my_worker_service')
            ->setDependencies($this->get('mailer'))
            ->build());
```

Now you just have to execute the worker:listen command to execute the queued worker:

```sh
$ php bin/console worker:listen
```

You can also specify the tube and the tries:

```sh
$ php bin/console worker:listen --tube=tube1 --tries=3
```

You can use supervisord to control the process and automatise the worker:listen command execution


##### TODO:

- Store the failed workers to the database