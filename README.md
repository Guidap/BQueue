
# BQueue Bundle by [@ArnaudTarroux](https://github.com/ArnaudTarroux/)

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
    adapter: beanstalkd # the value must be "sync" or "beanstalkd" (default: sync)
    host: 127.0.0.1
    port: 11300
    default: default    # the default tube name
    tries: 1            # the number of time the manager try a worker who failed 
```

#### Use

Create a Worker, this class must exetends 'Strnoar\BQueueBundle\Jobs\Jobs':

```sh
// MyBundle/Workers/ExampleWorker.php

<?php

namespace MyBundle\Workers;

use Strnoar\BQueueBundle\Jobs\Jobs;

class ExampleWorker implements JobsInterface
{   
    /**
     * @return mixed
     */
    public function handle(Array $parameters)
    {
        // Access to the data you will pass to the parameters array value 
        // when you dispatch the worker on queue
        // Do some stuff
    }
}
```

Now, just declare this one as service:

```sh
// MyBundle/Resources/config/services.yml

my_bundle.exemple_worker:
    class: MyBundle\Workers\ExampleWorker
```

You can access to the worker manager by the container with the id: 'bqueuebundle.job_manager'.

Here the dispatcher:

```sh
$this->get('bqueuebundle.job_manager')
            ->dispatch(
            // service is your worker service delaraction ID
            'my_bundle.exemple_worker'
            // parameters must be an array, you can pass some value in this one
            ['my_key' => 'my_value']
            );
```

if you need to inject dependencies in your worker you can use a method and use 'calls' in the service declaration:

```sh
// MyBundle/Workers/ExampleWorker.php

public function setDependencies(\Swift_Mailer $mailer)
{
    $this->mailer = $mailer;

    return $this;
}
```


Now you just have to execute the worker:listen command to execute the queued workers:

```sh
$ php bin/console worker:listen
```

You can also specify the tube and the tries:

```sh
$ php bin/console worker:listen --tube=tube1 --tries=3
```

I recommend to use supervisord to control the process and automatise the worker:listen command execution


##### TODO:

- Store the failed workers to the database
