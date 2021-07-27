<?php

namespace Samples\TaskService\Services;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\AbstractService;
use Micronative\ServiceSchema\Service\ServiceInterface;

class LogNotification extends AbstractService implements ServiceInterface
{
    public function consume(AbstractEvent $event = null)
    {
        echo "Notification has been logged for: {$event->getPayload()['name']}, {$event->getPayload()['email']}" . PHP_EOL;
    }
}
