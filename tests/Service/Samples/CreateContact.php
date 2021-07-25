<?php

namespace Micronative\ServiceSchema\Tests\Service\Samples;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\AbstractService;
use Micronative\ServiceSchema\Service\RollbackInterface;
use Micronative\ServiceSchema\Service\ServiceInterface;

class CreateContact extends AbstractService implements ServiceInterface, RollbackInterface
{
    public function consume(AbstractEvent $event = null)
    {
        return new SampleEvent();
    }

    public function rollback(AbstractEvent $event = null)
    {
       return 'Contact creation has been rollback.';
    }
}
