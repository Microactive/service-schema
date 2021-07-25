<?php

namespace Tests\Event;

use PHPUnit\Framework\TestCase;

class SampleEventTest extends TestCase
{
    /** @coversDefaultClass \Micronative\ServiceSchema\Event\AbstractEvent */

    /** @var string */
    protected $testDir;

    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
    }

    /**
     * @covers \Micronative\ServiceSchema\Event\AbstractEvent::__construct
     * @covers \Micronative\ServiceSchema\Event\AbstractEvent::toJson
     * @covers \Micronative\ServiceSchema\Event\AbstractEvent::getId
     * @covers \Micronative\ServiceSchema\Event\AbstractEvent::getName
     * @covers \Micronative\ServiceSchema\Event\AbstractEvent::getPayload
     * @covers \Micronative\ServiceSchema\Event\AbstractEvent::setId
     * @covers \Micronative\ServiceSchema\Event\AbstractEvent::setName
     * @covers \Micronative\ServiceSchema\Event\AbstractEvent::setPayload
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
    public function testToJson()
    {
        $event = new SampleEvent("SomeName");
        $event->setId('1')
            ->setName("Test.Event.Name")
            ->setPayload((object)["name" => "Ken"]);
        $this->assertSame($event->getId(), '1');
        $this->assertEquals((object)["name" => "Ken"], $event->getPayload());

        $json = $event->toJson();
        $this->assertTrue(is_string($json));
        $this->assertEquals('{"name":"Test.Event.Name","id":"1","payload":{"name":"Ken"}}', $json);

        $event = new SampleEvent("SomeName");
        $event->setName("Users.afterSaveCommit.Create");
        $event->setPayload(["user" => ["data" => ["name" => "Ken"]], "account" => ["data" => ["name" => "Brighte"]]]);
        $json = $event->toJson();
        $this->assertTrue(is_string($json));
        $this->assertEquals(
            '{"name":"Users.afterSaveCommit.Create","id":null,"payload":{"user":{"data":{"name":"Ken"}},"account":{"data":{"name":"Brighte"}}}}',
            $json
        );

        $event = new SampleEvent('Sample.Event', '111');
        $this->assertSame($event->getId(), '111');
        $this->assertSame($event->getName(), 'Sample.Event');
    }
}
