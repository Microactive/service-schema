<?php

namespace Samples\UserService\Events;

use Micronative\ServiceSchema\Event\AbstractEvent;

class UserEvent extends AbstractEvent
{
    /** @var \DateTime */
    private $createdAt;

    public function __construct(string $name, string $id = null, array $payload = null)
    {
        parent::__construct($name, $id, $payload);
        $this->createdAt = new \DateTime();
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        return json_encode(
            [
                "id" => $this->id,
                "name" => $this->name,
                "payload" => $this->payload,
                "created" => $this->createdAt->format('Y-m-d H:i:s')
            ]
        );
    }
}
