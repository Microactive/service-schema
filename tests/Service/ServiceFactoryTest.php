<?php

namespace Micronative\ServiceSchema\Tests\Service;

use PHPUnit\Framework\TestCase;
use Micronative\ServiceSchema\Service\ServiceFactory;
use Micronative\ServiceSchema\Service\ServiceInterface;

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
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     */
    public function testCreateService()
    {
        $serviceClass = "\Micronative\ServiceSchema\Tests\Service\Samples\CreateContact";
        $schema = $this->testDir . "/assets/schemas/CreateContact.json";
        $service = $this->serviceFactory->createService($serviceClass, $schema);
        $this->assertTrue($service instanceof ServiceInterface);
        $this->assertEquals($this->testDir . "/assets/schemas/CreateContact.json", $service->getJsonSchema());
    }
}
