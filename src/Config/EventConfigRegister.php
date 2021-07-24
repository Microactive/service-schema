<?php

namespace Micronative\ServiceSchema\Config;

use Micronative\ServiceSchema\Config\Exceptions\ConfigException;
use Micronative\ServiceSchema\Json\JsonReader;
use Symfony\Component\Yaml\Yaml;

class EventConfigRegister
{
    /** @var string[] $configFiles */
    protected $configFiles = [];

    /** @var \Micronative\ServiceSchema\Config\EventConfig[] $eventConfigs */
    protected $eventConfigs = [];

    /**
     * EventRegister constructor.
     *
     * @param array|null $files
     */
    public function __construct(array $files = null)
    {
        $this->configFiles = $files;
    }

    /**
     * @return \Micronative\ServiceSchema\Config\EventConfigRegister
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function loadEventConfigs()
    {
        if (empty($this->configFiles)) {
            return $this;
        }

        foreach ($this->configFiles as $file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            switch ($ext) {
                case 'json':
                    $this->loadFromJson($file);
                    break;
                case 'yml':
                    $this->loadFromYaml($file);
                    break;
                default:
                    throw new ConfigException(ConfigException::UNSUPPORTED_FILE_FORMAT . $ext);
            }
        }

        return $this;
    }

    /**
     * @param string $eventName
     * @return \Micronative\ServiceSchema\Config\EventConfig|null
     */
    public function retrieveEventConfig(string $eventName)
    {
        if (array_key_exists($eventName, $this->eventConfigs)) {
            return $this->eventConfigs[$eventName];
        }

        return null;
    }

    /**
     * @param \Micronative\ServiceSchema\Config\EventConfig $eventConfig
     * @return \Micronative\ServiceSchema\Config\EventConfigRegister
     */
    public function registerEventConfig(EventConfig $eventConfig)
    {
        $eventName = $eventConfig->getName();
        if (!isset($this->eventConfigs[$eventName])) {
            $this->eventConfigs[$eventName] = $eventConfig;
        } else {
            $currentConfig = $this->eventConfigs[$eventName];
            $eventConfig->setServiceClasses(array_merge($currentConfig->getServiceClasses(), $eventConfig->getServiceClasses()));
            $this->eventConfigs[$eventName] = $eventConfig;
        }

        return $this;
    }

    /**
     * @param string|null $file
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
    private function loadFromJson(string $file = null)
    {
        $events = JsonReader::decode(JsonReader::read($file), true);
        $this->loadFromArray($events);
    }

    /**
     * @param string|null $file
     */
    private function loadFromYaml(string $file = null)
    {
        $events = Yaml::parseFile($file);
        $this->loadFromArray($events);
    }

    /**
     * @param array|null $events
     */
    private function loadFromArray(array $events = null)
    {
        foreach ($events as $event) {
            if (!isset($event['event'])) {
                continue;
            }
            $name = $event['event'];
            $services = isset($event['services']) ? $event['services'] : null;
            $eventConfig = new EventConfig($name, $services);
            $this->registerEventConfig($eventConfig);
        }
    }

    /**
     * @return array
     */
    public function getConfigFiles()
    {
        return $this->configFiles;
    }

    /**
     * @param array|null $configFiles
     * @return \Micronative\ServiceSchema\Config\EventConfigRegister
     */
    public function setConfigFiles(array $configFiles = null)
    {
        $this->configFiles = $configFiles;

        return $this;
    }

    /**
     * @return \Micronative\ServiceSchema\Config\EventConfig[]
     */
    public function getEventConfigs()
    {
        return $this->eventConfigs;
    }

    /**
     * @param \Micronative\ServiceSchema\Config\EventConfig[]|null $eventConfigs
     * @return \Micronative\ServiceSchema\Config\EventConfigRegister
     */
    public function setEventConfigs(array $eventConfigs = null)
    {
        $this->eventConfigs = $eventConfigs;

        return $this;
    }
}
