<?php

namespace Tests\Service;

use Micronative\ServiceSchema\Config\ServiceConfig;
use Micronative\ServiceSchema\Exceptions\ServiceException;
use Micronative\ServiceSchema\Service\ServiceFactory;
use Micronative\ServiceSchema\Service\ServiceInterface;
use PHPUnit\Framework\TestCase;

class ServiceFactoryTest extends TestCase
{
    /** @var string */
    protected $testDir;

    /** @var ServiceFactory */
    protected $serviceFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->serviceFactory = new ServiceFactory();
    }

    /**
     * @covers \Micronative\ServiceSchema\Service\ServiceFactory::createService
     * @throws \Micronative\ServiceSchema\Exceptions\ServiceException
     */
    public function testCreateInvalidServiceClass()
    {
        $serviceClass = "\Tests\Service\Samples\InvalidServiceClass";
        $schema = $this->testDir . "/assets/schemas/services/CreateContact.json";
        $serviceConfig = new ServiceConfig($serviceClass, null, $schema);
        $this->expectException(ServiceException::class);
        $this->serviceFactory->createService($serviceConfig);
    }

    /**
     * @covers \Micronative\ServiceSchema\Service\ServiceFactory::createService
     * @throws \Micronative\ServiceSchema\Exceptions\ServiceException
     */
    public function testCreateInvalidService()
    {
        $serviceClass = "\Tests\Service\Samples\InvalidService";
        $schema = $this->testDir . "/assets/schemas/services/CreateContact.json";
        $serviceConfig = new ServiceConfig($serviceClass, null, $schema);
        $service = $this->serviceFactory->createService($serviceConfig);
        $this->assertFalse($service);
    }

    /**
     * @covers \Micronative\ServiceSchema\Service\ServiceFactory::createService
     * @throws \Micronative\ServiceSchema\Exceptions\ServiceException
     */
    public function testCreateService()
    {
        $serviceClass = "\Tests\Service\Samples\CreateContact";
        $schema = $this->testDir . "/assets/schemas/services/CreateContact.json";
        $serviceConfig = new ServiceConfig($serviceClass, null, $schema);
        $service = $this->serviceFactory->createService($serviceConfig);
        $this->assertTrue($service instanceof ServiceInterface);
        $this->assertEquals($this->testDir . "/assets/schemas/services/CreateContact.json", $service->getSchema());
    }
}
