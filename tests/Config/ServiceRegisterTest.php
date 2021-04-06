<?php

namespace Micronative\ServiceSchema\Tests\Config;

use Micronative\ServiceSchema\Config\Exception\ConfigException;
use Micronative\ServiceSchema\Config\ServiceRegister;
use PHPUnit\Framework\TestCase;

class ServiceRegisterTest extends TestCase
{
    /** @coversDefaultClass \Micronative\ServiceSchema\Config\ServiceRegister */
    protected $serviceRegister;

    /** @var string */
    protected $testDir;

    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->serviceRegister = new ServiceRegister(
            [$this->testDir . "/assets/configs/services.json", $this->testDir . "/assets/configs/services.yml"]
        );
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\ServiceRegister::loadServices
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exception\ConfigException
     */
    public function testLoadServicesWithEmptyConfigs()
    {
        $this->serviceRegister->setConfigs(null);
        $this->serviceRegister->loadServices();
        $this->assertEquals([], $this->serviceRegister->getServices());
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\ServiceRegister::loadServices
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exception\ConfigException
     */
    public function testLoadServicesWithUnsupportedFile()
    {
        $this->serviceRegister->setConfigs([$this->testDir . "/assets/configs/services.csv"]);
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage(ConfigException::UNSUPPORTED_FILE_FORMAT . 'csv');
        $this->serviceRegister->loadServices();
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\ServiceRegister::loadServices
     * @covers \Micronative\ServiceSchema\Config\ServiceRegister::loadFromJson
     * @covers \Micronative\ServiceSchema\Config\ServiceRegister::loadFromYaml
     * @covers \Micronative\ServiceSchema\Config\ServiceRegister::loadFromArray
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
        $noneExistingService = $this->serviceRegister->retrieveService("None.Existing.Name");
        $this->assertTrue(is_array($service));
        $this->assertTrue(isset($service["Service.Name"]));
        $this->assertEquals("SomeServiceSchema", $service["Service.Name"]['schema']);
        $this->assertNull($noneExistingService);
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\ServiceRegister::setConfigs
     * @covers \Micronative\ServiceSchema\Config\ServiceRegister::setServices
     * @covers \Micronative\ServiceSchema\Config\ServiceRegister::getConfigs
     * @covers \Micronative\ServiceSchema\Config\ServiceRegister::getServices
     */
    public function testSettersAndGetters()
    {
        $configs = [];
        $this->serviceRegister->setConfigs($configs);
        $this->assertEquals($configs, $this->serviceRegister->getConfigs());

        $services = [];
        $this->serviceRegister->setServices($services);
        $this->assertEquals($services, $this->serviceRegister->getServices());
    }
}
