<?php

namespace Samples\TaskService\Services;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\AbstractService;
use Micronative\ServiceSchema\Service\ServiceInterface;

class SendNotificationToNewUser extends AbstractService implements ServiceInterface
{
    public function consume(AbstractEvent $event = null)
    {
        echo 'Notification has been sent to new user.' . PHP_EOL;
    }
}
