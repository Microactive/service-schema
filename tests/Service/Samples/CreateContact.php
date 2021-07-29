<?php

namespace Tests\Service\Samples;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\AbstractService;
use Micronative\ServiceSchema\Service\RollbackInterface;
use Micronative\ServiceSchema\Service\ServiceInterface;

class CreateContact extends AbstractService implements RollbackInterface
{
    public function consume(AbstractEvent $event = null)
    {
        return new SampleEvent("SomeName");
    }

    public function rollback(AbstractEvent $event = null)
    {
       return 'Contact creation has been rollback.';
    }
}
