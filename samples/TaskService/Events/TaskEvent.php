<?php

namespace Samples\TaskService\Events;

use Micronative\ServiceSchema\Event\AbstractEvent;

class TaskEvent extends AbstractEvent
{
    /** @var \DateTime */
    private $receivedAt;

    public function __construct(string $name, string $id = null, array $payload = null)
    {
        $this->name = $name;
        $this->id = $id;
        $this->payload = $payload;
        $this->receivedAt = new \DateTime();
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
                "payload" => $this->payload,
                "received_at" => $this->receivedAt->format('Y-m-d H:i:s')
            ]
        );
    }

    /**
     * @param string $jsonString
     * @return \Samples\TaskService\Events\TaskEvent
     */
    public function unserialize(string $jsonString)
    {
        $data = json_decode($jsonString, true);
        $this->name = isset($data['name']) ? $data['name'] : null;
        $this->id = isset($data['id']) ? $data['id'] : null;
        $this->payload = isset($data['payload']) ? $data['payload'] : null;

        return $this;
    }
}
