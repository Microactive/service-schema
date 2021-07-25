<?php

namespace Tests\Service\Samples;

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
}
