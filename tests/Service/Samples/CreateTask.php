<?php

namespace Micronative\ServiceSchema\Tests\Service\Samples;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\AbstractService;
use Micronative\ServiceSchema\Service\ServiceInterface;

class CreateTask extends AbstractService implements ServiceInterface
{
    public function consume(AbstractEvent $event = null)
    {
        return 'Task created.';
    }
}
