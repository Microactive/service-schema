<?php

namespace Tests\Config;

use Micronative\ServiceSchema\Config\EventConfig;
use Micronative\ServiceSchema\Config\ServiceConfig;
use PHPUnit\Framework\TestCase;

class ServiceConfigTest extends TestCase
{
    /** @coversDefaultClass \Micronative\ServiceSchema\Config\ServiceConfig */
    private $serviceConfig;

    public function testSettersAndGetters()
    {
        $this->serviceConfig = new ServiceConfig('Service.Class');
        $this->serviceConfig
            ->setClass('SomeClass')
            ->setAlias('SomeAlias')
            ->setSchema('SomeSchema')
            ->setCallbacks(['SomeCallbacks']);

        $this->assertEquals('SomeClass', $this->serviceConfig->getClass());
        $this->assertEquals('SomeAlias', $this->serviceConfig->getAlias());
        $this->assertEquals('SomeSchema', $this->serviceConfig->getSchema());
        $this->assertEquals(['SomeCallbacks'], $this->serviceConfig->getCallbacks());
    }
}
