<?php

namespace Micronative\ServiceSchema\Config;

use Micronative\ServiceSchema\Config\Exception\ConfigException;
use Micronative\ServiceSchema\Json\JsonReader;
use Symfony\Component\Yaml\Yaml;

class EventRegister
{
    /** @var array $configs */
    protected $configs = [];

    /** @var array $events */
    protected $events = [];

    /**
     * EventRegister constructor.
     *
     * @param array|null $configs
     */
    public function __construct(array $configs = null)
    {
        $this->configs = $configs;
    }

    /**
     * @return \Micronative\ServiceSchema\Config\EventRegister
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exception\ConfigException
     */
    public function loadEvents()
    {
        if (empty($this->configs)) {
            return $this;
        }

        foreach ($this->configs as $config) {
            $ext = pathinfo($config, PATHINFO_EXTENSION);
            switch ($ext) {
                case 'json':
                    $this->loadFromJson($config);
                    break;
                case 'yml':
                    $this->loadFromYaml($config);
                    break;
                default:
                    throw new ConfigException(ConfigException::UNSUPPORTED_FILE_FORMAT . $ext);
            }
        }

        return $this;
    }

    /**
     * @param string|null $eventName
     * @param array|null $services
     * @return \Micronative\ServiceSchema\Config\EventRegister
     */
    public function registerEvent(string $eventName = null, array $services = null)
    {
        if (!isset($this->events[$eventName])) {
            $this->events[$eventName] = $services;
        } else {
            $this->events[$eventName] = array_unique(array_merge($this->events[$eventName], $services));
        }

        return $this;
    }

    /**
     * @param string|null $eventName
     * @return array|null
     */
    public function retrieveEvent(string $eventName = null)
    {
        if (array_key_exists($eventName, $this->events)) {
            return [$eventName => $this->events[$eventName]];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * @param array|null $configs
     * @return \Micronative\ServiceSchema\Config\EventRegister
     */
    public function setConfigs(array $configs = null)
    {
        $this->configs = $configs;

        return $this;
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param array|null $events
     * @return \Micronative\ServiceSchema\Config\EventRegister
     */
    public function setEvents(array $events = null)
    {
        $this->events = $events;

        return $this;
    }

    /**
     * @param string|null $file
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    protected function loadFromJson(string $file = null)
    {
        $events = JsonReader::decode(JsonReader::read($file), true);
        $this->loadFromArray($events);
    }

    /**
     * @param string|null $file
     */
    protected function loadFromYaml(string $file = null)
    {
        $events = Yaml::parseFile($file);
        $this->loadFromArray($events);
    }

    /**
     * @param array|null $events
     */
    protected function loadFromArray(array $events = null)
    {
        foreach ($events as $event) {
            if(empty($event['event'])){
                continue;
            }
            $eventName = $event['event'];
            $services = isset($event['services']) ? $event['services'] : null;
            $this->registerEvent($eventName, $services);
        }
    }
}
