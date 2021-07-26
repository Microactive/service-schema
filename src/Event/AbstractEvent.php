<?php

namespace Micronative\ServiceSchema\Event;

use Micronative\ServiceSchema\Json\JsonReader;

abstract class AbstractEvent
{
    /** @var string */
    protected $name;

    /** @var string|null */
    protected $id;

    /** @var array|null */
    protected $payload;

    /** @var string|null */
    protected $schema;

    /**
     * AbstractEvent constructor.
     * @param string $name
     * @param string|null $id
     * @param array|null $payload
     * @param string|null $schema
     */
    public function __construct(string $name, string $id = null, array $payload = null, string $schema = null)
    {
        $this->name = $name;
        $this->id = $id;
        $this->payload = $payload;
        $this->schema = $schema;
    }

    /**
     * Get the json representing the event
     * Override this function to return more properties if as necessary
     * But name is required
     *
     * @return false|string
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
     public function toJson()
     {
         return JsonReader::encode(
             [
                 "name" => $this->name,
                 "id" => $this->id,
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
     * @return array|null
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param array|null $payload
     * @return \Micronative\ServiceSchema\Event\AbstractEvent
     */
    public function setPayload($payload = null)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSchema(): ?string
    {
        return $this->schema;
    }

    /**
     * @param string|null $schema
     * @return \Micronative\ServiceSchema\Event\AbstractEvent
     */
    public function setSchema(?string $schema): AbstractEvent
    {
        $this->schema = $schema;

        return $this;
    }
}
