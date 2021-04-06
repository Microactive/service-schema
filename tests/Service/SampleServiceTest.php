<?php

namespace Micronative\ServiceSchema\Tests\Service;

use Micronative\ServiceSchema\Tests\Service\Samples\CreateContact;
use Micronative\ServiceSchema\Tests\Service\Samples\SampleContainer;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;


class SampleServiceTest extends TestCase
{
    /** @coversDefaultClass \Micronative\ServiceSchema\Tests\Service\Samples\CreateContact */
    protected $sampleService;

    public function setUp(): void
    {
        parent::setUp();
        $this->sampleService = new CreateContact();
    }

    public function testSettersAndGetters()
    {
        $this->sampleService
            ->setName('Create.Contact')
            ->setJsonSchema('json_schema_file')
            ->setContainer(new SampleContainer());

        $this->assertEquals('Create.Contact', $this->sampleService->getName());
        $this->assertEquals('json_schema_file', $this->sampleService->getJsonSchema());
        $this->assertInstanceOf(ContainerInterface::class, $this->sampleService->getContainer());
    }
}
