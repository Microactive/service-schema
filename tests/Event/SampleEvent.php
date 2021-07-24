<?php

namespace Micronative\ServiceSchema\Tests\Event;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Json\JsonReader;

class SampleEvent extends AbstractEvent
{
    /**
     * @return false|string
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
    public function toJson()
    {
        return JsonReader::encode(
            [
                "id" => $this->id,
                "name" => $this->name,
                "payload" => $this->payload,
            ]
        );
    }

    /**
     * @param array|null $data
     * @return \Micronative\ServiceSchema\Event\AbstractEvent|void
     */
    public function setData(array $data = null)
    {
        $this->id = isset($data['id']) ? $data['id'] : null;
        $this->name = isset($data['name']) ? $data['name'] : null;
        $this->payload = isset($data['payload']) ? $data['payload'] : null;
    }
}
