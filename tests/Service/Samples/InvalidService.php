<?php

namespace Micronative\ServiceSchema\Tests\Service\Samples;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\AbstractService;

class InvalidService extends AbstractService
{
    public function consume(AbstractEvent $event = null)
    {
        return false;
    }
}
