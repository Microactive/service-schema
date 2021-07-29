<?php

namespace Samples\TaskService\Services;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\AbstractService;

class LogTask extends AbstractService
{
    public function consume(AbstractEvent $event = null)
    {
        echo "Task has been logged for: {$event->getPayload()['name']}, {$event->getPayload()['email']}" . PHP_EOL;
    }
}
