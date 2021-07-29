<?php

namespace Samples\TaskService\Services;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\AbstractService;

class SendNotificationToUpdatedUser extends AbstractService
{
    public function consume(AbstractEvent $event = null)
    {
        echo "Notification has been sent to updated user: {$event->getPayload()['name']}, {$event->getPayload()['email']}" . PHP_EOL;

        return $event;
    }
}
