<?php

namespace Tests\Service\Samples;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\AbstractService;
use Micronative\ServiceSchema\Service\ServiceInterface;

class PushMessageToSqs extends AbstractService implements ServiceInterface
{
    public function consume(AbstractEvent $event = null)
    {
        return true;
    }
}
