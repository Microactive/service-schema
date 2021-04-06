<?php

namespace Micronative\ServiceSchema\Tests\Json;

use PHPUnit\Framework\TestCase;
use Micronative\ServiceSchema\Json\Exception\JsonException;
use Micronative\ServiceSchema\Json\JsonReader;

class JsonReaderTest extends TestCase
{
    /** @coversDefaultClass \Micronative\ServiceSchema\Json\JsonReader */

    /** @var string */
    protected $testDir;

    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
    }

    /**
     * @covers \Micronative\ServiceSchema\Json\JsonReader::read
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testReadEmptyFile()
    {
        $this->expectException(JsonException::class);
        JsonReader::read(null);
    }

    /**
     * @covers \Micronative\ServiceSchema\Json\JsonReader::read
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testReadInvalidFile()
    {
        $this->expectException(JsonException::class);
        JsonReader::read("someinvalidfile");
    }

    /**
     * @covers \Micronative\ServiceSchema\Json\JsonReader::decode
     * @covers \Micronative\ServiceSchema\Json\JsonReader::read
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testDecodeFailed()
    {
        $this->expectException(JsonException::class);
        JsonReader::decode(null);
    }

    /**
     * @covers \Micronative\ServiceSchema\Json\JsonReader::read
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testRead()
    {
        $file = $this->testDir . "/assets/files/read.json";
        $json = JsonReader::read($file);
        $this->assertTrue(is_string($json));
    }

    /**
     * @covers \Micronative\ServiceSchema\Json\JsonReader::read
     * @covers \Micronative\ServiceSchema\Json\JsonReader::decode
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testDecode()
    {
        $file = $this->testDir . "/assets/files/read.json";
        $json = JsonReader::read($file);
        $object = JsonReader::decode($json);

        $this->assertTrue(is_object($object));
        $this->assertEquals("Users.afterSaveCommit.Create", $object->event);
        $this->assertEquals("20190726032212", $object->time);
        $this->assertTrue(isset($object->payload));
        $this->assertEquals("Ken", $object->payload->user->data->name);
        $this->assertTrue(isset($object->payload->account->data->name));
        $this->assertEquals("Brighte", $object->payload->account->data->name);
    }

    /**
     * @covers \Micronative\ServiceSchema\Json\JsonReader::encode
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testEncodeFailed()
    {
        $this->expectException(JsonException::class);
        JsonReader::encode(null);
    }

    /**
     * @covers \Micronative\ServiceSchema\Json\JsonReader::encode
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testEncode()
    {
        $array = ["name" => "Ken"];
        $json = JsonReader::encode($array);
        $this->assertTrue(is_string($json));
    }

    /**
     * @covers \Micronative\ServiceSchema\Json\JsonReader::save
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testSaveEmptyFile()
    {
        $this->expectException(JsonException::class);
        $this->expectExceptionMessage(JsonException::MISSING_JSON_FILE);
        JsonReader::save(null);
    }

    /**
     * @covers \Micronative\ServiceSchema\Json\JsonReader::save
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testSaveEmptyContent()
    {
        $file = $this->testDir . "/assets/files/save.json";
        $this->expectException(JsonException::class);
        $this->expectExceptionMessage(JsonException::MISSING_JSON_CONTENT);
        JsonReader::save($file, null);
    }

    /**
     * @covers \Micronative\ServiceSchema\Json\JsonReader::save
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testSave()
    {
        $file = $this->testDir . "/assets/files/save.json";
        $array = ["name" => "Ken"];
        $json = JsonReader::encode($array);
        JsonReader::save($file, $json);
        $contents = file_get_contents($file);
        $this->assertSame('{"name":"Ken"}', $contents);
    }
}
