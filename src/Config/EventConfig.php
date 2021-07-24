<?php

namespace Micronative\ServiceSchema\Config;

class EventConfig
{
    /** @var string */
    protected $name;

    /** @var string[] */
    protected $serviceClasses;

    /**
     * EventConfig constructor.
     * @param string $name
     * @param array|null $services
     */
    public function __construct(string $name, array $services = null)
    {
        $this->name = $name;
        $this->serviceClasses = $services;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \Micronative\ServiceSchema\Config\EventConfig
     */
    public function setName(string $name): EventConfig
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getServiceClasses(): ?array
    {
        return $this->serviceClasses;
    }

    /**
     * @param string[] $serviceClasses
     * @return \Micronative\ServiceSchema\Config\EventConfig
     */
    public function setServiceClasses(array $serviceClasses): EventConfig
    {
        $this->serviceClasses = $serviceClasses;

        return $this;
    }
}
