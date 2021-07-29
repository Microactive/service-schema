<?php

namespace Micronative\ServiceSchema\Event;

use JsonSerializable;

abstract class AbstractEvent implements JsonSerializable
{
    /** @var string */
    protected $name;

    /** @var string|null */
    protected $id;

    /** @var array|null */
    protected $payload;

    /**
     * @var string relative path (from Processor::schemaDir) to json schema file
     * @@see \Micronative\ServiceSchema\Processor::schemaDir
     */
    protected $schema;

    /**
     * Get the json string representing the event
     * name is required
     *
     * @return false|string
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
    abstract public function jsonSerialize();
    /**
     * {
     *   return json_encode(
     *     [
     *       "name" => $this->name,
     *       "id" => $this->id,
     *       "payload" => $this->payload,
     *     ]
     *   );
     * }
     */

    /**
     * Set event properties from json string
     * @param string $jsonString
     * @return \Micronative\ServiceSchema\Event\AbstractEvent
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
    abstract public function unserialize(string $jsonString);
    /**
     * {
     *   $data = json_decode($jsonString, true);
     *   $this->name = isset($data['name']) ? $data['name'] : null;
     *   $this->id = isset($data['id']) ? $data['id'] : null;
     *   $this->payload = isset($data['payload']) ? $data['payload'] : null;
     *
     *   return $this;
     * }
     */

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
