<?php

namespace Samples\TaskService\Services;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\AbstractService;
use Micronative\ServiceSchema\Service\RollbackInterface;
use Micronative\ServiceSchema\Service\ServiceInterface;

class CreateTaskForNewUser extends AbstractService implements ServiceInterface, RollbackInterface
{
    public function consume(AbstractEvent $event = null)
    {
        echo 'Task has been created for new user.' . PHP_EOL;

        return $event;
    }

    public function rollback(AbstractEvent $event = null)
    {
        echo 'Task has been rollback.' . PHP_EOL;

        return $event;
    }
}
