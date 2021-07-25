<?php

namespace Micronative\ServiceSchema\Event;

use Micronative\ServiceSchema\Json\JsonReader;

abstract class AbstractEvent
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $name;

    /** @var array|null|\stdClass */
    protected $payload;

    /**
     * AbstractEvent constructor.
     * @param string $name
     * @param string|null $id
     * @param array|\stdClass|null $payload
     */
    public function __construct(string $name, string $id = null,  $payload = null)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setPayload($payload);
    }

    /**
     * Get the json representing the event
     * Override this function to return more properties if as necessary
     * But id, name and payload are compulsory
     *
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
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return \Micronative\ServiceSchema\Event\AbstractEvent
     */
    public function setId(string $id = null)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return \Micronative\ServiceSchema\Event\AbstractEvent
     */
    public function setName(string $name = null)
    {
        $this->name = $name;

        return $this;
    }


    /**
     * @return array|\stdClass|null
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param array|\stdClass|null $payload
     * @return \Micronative\ServiceSchema\Event\AbstractEvent
     */
    public function setPayload($payload = null)
    {
        $this->payload = (object)$payload;

        return $this;
    }
}
