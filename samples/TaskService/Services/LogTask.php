<?php

namespace Samples\TaskService\Services;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\AbstractService;
use Micronative\ServiceSchema\Service\ServiceInterface;

class LogTask extends AbstractService implements ServiceInterface
{
    public function consume(AbstractEvent $event = null)
    {
        echo 'Task has been logged.' . PHP_EOL;
    }
}
