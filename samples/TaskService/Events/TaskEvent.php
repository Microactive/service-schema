<?php

namespace Samples\TaskService\Events;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Json\JsonReader;

class TaskEvent extends AbstractEvent
{
    /** @var \DateTime */
    private $receivedAt;

    public function __construct(string $name, string $id = null, $payload = null)
    {
        parent::__construct($name, $id, $payload);
        $this->receivedAt = new \DateTime();
    }

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
                "received" => date('Y-m-d:H:i:s', time())
            ]
        );
    }
}
