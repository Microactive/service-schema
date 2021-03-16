<?php

namespace Micronative\ServiceSchema\Tests\Config;

use PHPUnit\Framework\TestCase;
use Micronative\ServiceSchema\Config\ServiceRegister;

class ServiceRegisterTest extends TestCase
{
    /** @var string */
    protected $testDir;

    /** @var ServiceRegister */
    protected $serviceRegister;

    public function setUp()
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->serviceRegister = new ServiceRegister([$this->testDir . "/assets/configs//services.json"]);
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\ServiceRegister::loadServices
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exception\ConfigException
     */
    public function testLoadServices()
    {
        $this->serviceRegister->loadServices();
        $services = $this->serviceRegister->getServices();
        $this->assertTrue(is_array($services));
        $this->assertTrue(isset($services["Micronative\ServiceSchema\Tests\Service\Samples\CreateContact"]));
        $this->assertTrue(isset($services["Micronative\ServiceSchema\Tests\Service\Samples\UpdateContact"]));
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\ServiceRegister::registerService
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exception\ConfigException
     */
    public function testRegisterService()
    {
        $this->serviceRegister->loadServices();
        $this->serviceRegister->registerService("Service.Name", "SomeServiceSchema");
        $services = $this->serviceRegister->getServices();
        $this->assertTrue(is_array($services));
        $this->assertTrue(isset($services["Service.Name"]));
        $this->assertEquals("SomeServiceSchema", $services["Service.Name"]['schema']);
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\ServiceRegister::retrieveService
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exception\ConfigException
     */
    public function testRetrieveEvent()
    {
        $this->serviceRegister->loadServices();
        $this->serviceRegister->registerService("Service.Name", "SomeServiceSchema");
        $service = $this->serviceRegister->retrieveService("Service.Name");
        $this->assertTrue(is_array($service));
        $this->assertTrue(isset($service["Service.Name"]));
        $this->assertEquals("SomeServiceSchema", $service["Service.Name"]['schema']);
    }
}
