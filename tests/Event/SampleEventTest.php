<?php

namespace Micronative\ServiceSchema\Tests\Event;

use PHPUnit\Framework\TestCase;

class SampleEventTest extends TestCase
{
    /** @var string */
    protected $testDir;

    public function setUp()
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
    }

    /**
     * @covers \Micronative\ServiceSchema\Event\AbstractEvent::toJson
     * @covers \Micronative\ServiceSchema\Event\AbstractEvent::getId
     * @covers \Micronative\ServiceSchema\Event\AbstractEvent::getName
     * @covers \Micronative\ServiceSchema\Event\AbstractEvent::getPayload
     * @covers \Micronative\ServiceSchema\Event\AbstractEvent::setId
     * @covers \Micronative\ServiceSchema\Event\AbstractEvent::setName
     * @covers \Micronative\ServiceSchema\Event\AbstractEvent::setPayload
     * @covers \Micronative\ServiceSchema\Event\AbstractEvent::setData
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testToJson()
    {
        $event = new SampleEvent();
        $event->setName("Test.Event.Name");
        $event->setPayload((object) ["name" => "Ken"]);

        $json = $event->toJson();
        $this->assertTrue(is_string($json));
        $this->assertEquals('{"id":null,"name":"Test.Event.Name","payload":{"name":"Ken"}}', $json);

        $event = new SampleEvent();
        $event->setName("Users.afterSaveCommit.Create");
        $event->setPayload(["user" => ["data" => ["name" => "Ken"]], "account" => ["data" => ["name" => "Brighte"]]]);
        $json = $event->toJson();
        $this->assertTrue(is_string($json));
        $this->assertEquals('{"id":null,"name":"Users.afterSaveCommit.Create","payload":{"user":{"data":{"name":"Ken"}},"account":{"data":{"name":"Brighte"}}}}', $json);

        $event = new SampleEvent();
        $event->setId(111);
        $id = $event->getId();
        $this->assertSame($id, '111');
    }
}
