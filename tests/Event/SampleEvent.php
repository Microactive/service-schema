<?php

namespace Tests\Event;

use Micronative\ServiceSchema\Event\AbstractEvent;

class SampleEvent extends AbstractEvent
{
    public function __construct(string $name, string $id = null, array $payload = null)
    {
        $this->name = $name;
        $this->id = $id;
        $this->payload = $payload;
    }

    /**
     * @return false|string
     */
    public function jsonSerialize()
    {
        return json_encode(
            [
                "name" => $this->name,
                "id" => $this->id,
                "payload" => $this->payload
            ]
        );
    }

    /**
     * @param string $jsonString
     * @return \Tests\Event\SampleEvent
     */
    public function jsonUnserialize(string $jsonString)
    {
        $data = json_decode($jsonString, true);
        $this->name = isset($data['name']) ? $data['name'] : null;
        $this->id = isset($data['id']) ? $data['id'] : null;
        $this->payload = isset($data['payload']) ? $data['payload'] : null;

        return $this;
    }
}
