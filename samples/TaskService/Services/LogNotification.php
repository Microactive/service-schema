<?php

namespace Samples\TaskService\Services;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\AbstractService;

class LogNotification extends AbstractService
{
    public function consume(AbstractEvent $event = null)
    {
        echo "Notification has been logged for: {$event->getPayload()['name']}, {$event->getPayload()['email']}" . PHP_EOL;
    }
}
