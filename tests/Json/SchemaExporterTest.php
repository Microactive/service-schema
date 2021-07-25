<?php

namespace Tests\Json;

use PHPUnit\Framework\TestCase;
use Micronative\ServiceSchema\Json\JsonReader;
use Micronative\ServiceSchema\Json\SchemaExporter;
use Micronative\ServiceSchema\Processor;

class SchemaExporterTest extends TestCase
{
    /** @coversDefaultClass \Micronative\ServiceSchema\Json\SchemaExporter */
    protected $schemaExporter;

    /** @var string */
    protected $testDir;

    /** @var string */
    protected $message;

    /** @var \Micronative\ServiceSchema\Processor */
    protected $processor;

    /**
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
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
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
    public function testExportJson()
    {
        $this->schemaExporter = new SchemaExporter($this->processor);

        $result = $this->schemaExporter->export(schemaExporter::RETURN_JSON);
        $this->assertStringContainsString('{"CreateContact":{"type":"object","properties":{"name":{"type":"string","minLength":0,"maxLength":256}', $result);
    }

    /**
     * @covers \Micronative\ServiceSchema\Json\SchemaExporter::__construct
     * @covers \Micronative\ServiceSchema\Json\SchemaExporter::export
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
    public function testExportArray()
    {
        $this->schemaExporter = new SchemaExporter($this->processor);

        $result = $this->schemaExporter->export(schemaExporter::RETURN_ARRAY);
        $this->assertIsArray($result);
    }

    /**
     * @covers \Micronative\ServiceSchema\Json\SchemaExporter::__construct
     * @covers \Micronative\ServiceSchema\Json\SchemaExporter::export
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
    public function testExportDefault()
    {
        $this->schemaExporter = new SchemaExporter($this->processor);

        $result = $this->schemaExporter->export();
        $this->assertIsArray($result);
    }
}
