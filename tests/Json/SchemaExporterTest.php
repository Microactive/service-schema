<?php

namespace Micronative\ServiceSchema\Tests\Json;

use PHPUnit\Framework\TestCase;
use Micronative\ServiceSchema\Json\JsonReader;
use Micronative\ServiceSchema\Json\SchemaExporter;
use Micronative\ServiceSchema\Main\Processor;

class SchemaExporterTest extends TestCase
{
    /** @var string */
    protected $testDir;

    /** @var string */
    protected $message;

    /** @var \Micronative\ServiceSchema\Main\Processor */
    protected $processor;

    /** @var \Micronative\ServiceSchema\Json\SchemaExporter */
    protected $schemaExporter;


    /**
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->processor = new Processor([$this->testDir . "/assets/configs/events.json"], [$this->testDir . "/assets/configs/services.json"], $this->testDir);
        $this->message = JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json");
        $this->schema = JsonReader::read($this->testDir . "/assets/schemas/CreateContact.json");

    }

    /**
     * @covers \Micronative\ServiceSchema\Json\SchemaExporter::__construct
     * @covers \Micronative\ServiceSchema\Json\SchemaExporter::export
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testExport()
    {
        $this->schemaExporter = new SchemaExporter($this->processor);

        $result = $this->schemaExporter->export(schemaExporter::RETURN_JSON);
        $this->assertStringContainsString('{"CreateContact":{"type":"object","properties":{"name":{"type":"string","minLength":0,"maxLength":256}', $result);
    }
}
