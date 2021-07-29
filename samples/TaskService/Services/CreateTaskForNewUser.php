<?php

namespace Samples\TaskService\Services;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\AbstractService;
use Micronative\ServiceSchema\Service\RollbackInterface;

class CreateTaskForNewUser extends AbstractService implements RollbackInterface
{
    public function consume(AbstractEvent $event = null)
    {
        echo "Task has been created for new user: {$event->getPayload()['name']}, {$event->getPayload()['email']}" . PHP_EOL;

        return $event;
    }

    public function rollback(AbstractEvent $event = null)
    {
        echo 'Task has been rollback.' . PHP_EOL;

        return $event;
    }
}
