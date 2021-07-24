<?php

namespace Micronative\ServiceSchema\Event;

abstract class AbstractEvent
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $name;

    /** @var array|null|\stdClass */
    protected $payload;


    /**
     * Event constructor.
     *
     * @param array|null $data
     */
    public function __construct(array $data = null)
    {
        $this->setData($data);
    }

    /**
     * Set the event properties from an array data.
     * This function is called in the construct to set event properties
     *
     * @param array|null $data
     * @return \Micronative\ServiceSchema\Event\AbstractEvent
     */
    abstract public function setData(array $data = null);

    /**
     * Get the json representing the event
     *
     * @return false|string
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
    abstract public function toJson();


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
        $this->payload = $payload;

        return $this;
    }
}
