<?php

namespace Micronative\ServiceSchema\Tests\Config;

use Micronative\ServiceSchema\Config\Exceptions\ConfigException;
use Micronative\ServiceSchema\Config\ServiceConfig;
use Micronative\ServiceSchema\Config\ServiceConfigRegister;
use PHPUnit\Framework\TestCase;

class ServiceConfigRegisterTest extends TestCase
{
    /** @coversDefaultClass \Micronative\ServiceSchema\Config\ServiceConfigRegister */
    protected $serviceConfigRegister;

    /** @var string */
    protected $testDir;

    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->serviceConfigRegister = new ServiceConfigRegister(
            [$this->testDir . "/assets/configs/services.json", $this->testDir . "/assets/configs/services.yml"]
        );
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\ServiceConfigRegister::loadServiceConfigs
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function testLoadServicesWithEmptyConfigs()
    {
        $this->serviceConfigRegister->setConfigFiles(null);
        $this->serviceConfigRegister->loadServiceConfigs();
        $this->assertEquals([], $this->serviceConfigRegister->getServiceConfigs());
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\ServiceConfigRegister::loadServiceConfigs
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function testLoadServicesWithUnsupportedFile()
    {
        $this->serviceConfigRegister->setConfigFiles([$this->testDir . "/assets/configs/services.csv"]);
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage(ConfigException::UNSUPPORTED_FILE_FORMAT . 'csv');
        $this->serviceConfigRegister->loadServiceConfigs();
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\ServiceConfigRegister::loadServiceConfigs
     * @covers \Micronative\ServiceSchema\Config\ServiceConfigRegister::loadFromJson
     * @covers \Micronative\ServiceSchema\Config\ServiceConfigRegister::loadFromYaml
     * @covers \Micronative\ServiceSchema\Config\ServiceConfigRegister::loadFromArray
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function testLoadServices()
    {
        $this->serviceConfigRegister->loadServiceConfigs();
        $services = $this->serviceConfigRegister->getServiceConfigs();
        $this->assertTrue(is_array($services));
        $this->assertTrue(isset($services["Micronative\ServiceSchema\Tests\Service\Samples\CreateContact"]));
        $this->assertTrue(isset($services["Micronative\ServiceSchema\Tests\Service\Samples\UpdateContact"]));
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\ServiceConfigRegister::retrieveServiceConfig
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function testRegisterServiceConfigs()
    {
        $this->serviceConfigRegister->loadServiceConfigs();
        $config = new ServiceConfig("Service.Name", "serviceName","SomeServiceSchema");
        $this->serviceConfigRegister->registerServiceConfig($config);
        $serviceConfigs = $this->serviceConfigRegister->getServiceConfigs();
        $serviceConfig = $serviceConfigs['Service.Name'];

        $this->assertIsArray($serviceConfigs);
        $this->assertArrayHasKey("Service.Name", $serviceConfigs);
        $this->assertInstanceOf(ServiceConfig::class, $serviceConfig);
        $this->assertEquals('Service.Name', $serviceConfig->getClass());
        $this->assertEquals('serviceName', $serviceConfig->getAlias());
        $this->assertEquals("SomeServiceSchema", $serviceConfig->getSchema());
        $this->assertNull($serviceConfig->getCallbacks());
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\ServiceConfigRegister::retrieveServiceConfig
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function testRetrieveServiceConfig()
    {
        $this->serviceConfigRegister->loadServiceConfigs();
        $config = new ServiceConfig("Service.Name", "serviceName","SomeServiceSchema");
        $this->serviceConfigRegister->registerServiceConfig($config);
        $serviceConfig = $this->serviceConfigRegister->retrieveServiceConfig('Service.Name');

        $this->assertInstanceOf(ServiceConfig::class, $serviceConfig);
        $this->assertEquals('Service.Name', $serviceConfig->getClass());
        $this->assertEquals('serviceName', $serviceConfig->getAlias());
        $this->assertEquals("SomeServiceSchema", $serviceConfig->getSchema());
        $this->assertNull($serviceConfig->getCallbacks());
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\ServiceConfigRegister::retrieveServiceConfig
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function testRetrieveServiceConfigByAlias()
    {
        $this->serviceConfigRegister->loadServiceConfigs();
        $config = new ServiceConfig("Service.Name", "serviceNameAlias","SomeServiceSchema");
        $this->serviceConfigRegister->registerServiceConfig($config);
        $serviceConfig = $this->serviceConfigRegister->retrieveServiceConfig('serviceNameAlias');
        $noneExistingConfig = $this->serviceConfigRegister->retrieveServiceConfig('noneExistingConfig');

        $this->assertInstanceOf(ServiceConfig::class, $serviceConfig);
        $this->assertEquals('Service.Name', $serviceConfig->getClass());
        $this->assertEquals('serviceNameAlias', $serviceConfig->getAlias());
        $this->assertEquals("SomeServiceSchema", $serviceConfig->getSchema());
        $this->assertNull($serviceConfig->getCallbacks());
        $this->assertNull($noneExistingConfig);
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\ServiceConfigRegister::getConfigFiles
     * @covers \Micronative\ServiceSchema\Config\ServiceConfigRegister::setConfigFiles
     * @covers \Micronative\ServiceSchema\Config\ServiceConfigRegister::getServiceConfigs
     * @covers \Micronative\ServiceSchema\Config\ServiceConfigRegister::setServiceConfigs
     */
    public function testSettersAndGetters()
    {
        $configs = [];
        $this->serviceConfigRegister->setConfigFiles($configs);
        $this->assertEquals($configs, $this->serviceConfigRegister->getConfigFiles());

        $services = [];
        $this->serviceConfigRegister->setServiceConfigs($services);
        $this->assertEquals($services, $this->serviceConfigRegister->getServiceConfigs());
    }
}
