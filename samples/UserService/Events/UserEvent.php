<?php

namespace Samples\UserService\Events;

use Micronative\ServiceSchema\Event\AbstractEvent;

class UserEvent extends AbstractEvent
{
    /** @var \DateTime */
    private $createdAt;

    public function __construct(string $name, string $id = null, array $payload = null)
    {
        $this->name = $name;
        $this->id = $id;
        $this->payload = $payload;
        $this->createdAt = new \DateTime();
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
                "created_at" => $this->createdAt->format('Y-m-d H:i:s')
            ]
        );
    }

    /**
     * @param string $jsonString
     * @return \Samples\UserService\Events\UserEvent
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
