<?php

namespace Micronative\ServiceSchema\Tests\Config;

use Micronative\ServiceSchema\Config\EventConfig;
use PHPUnit\Framework\TestCase;

class EventConfigTest extends TestCase
{
    /** @coversDefaultClass \Micronative\ServiceSchema\Config\EventConfig */
    private $eventConfig;

    public function testSettersAndGetters()
    {
        $this->eventConfig = new EventConfig('Event.Name');
        $this->eventConfig
            ->setName('SomeName')
            ->setServiceClasses(['ServiceClass']);

        $this->assertEquals('SomeName', $this->eventConfig->getName());
        $this->assertEquals(['ServiceClass'], $this->eventConfig->getServiceClasses());
    }
}
