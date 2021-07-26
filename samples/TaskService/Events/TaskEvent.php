<?php

namespace Samples\TaskService\Events;

use Micronative\ServiceSchema\Event\AbstractEvent;

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
     * @return array
     */
    public function toArray()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "payload" => $this->payload,
            "received" => date('Y-m-d:H:i:s', time())
        ];
    }
}
