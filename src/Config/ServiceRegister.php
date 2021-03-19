<?php

namespace Micronative\ServiceSchema\Config;

use Micronative\ServiceSchema\Config\Exception\ConfigException;
use Micronative\ServiceSchema\Json\JsonReader;
use Symfony\Component\Yaml\Yaml;

class ServiceRegister
{
    /** @var array $configs */
    protected $configs = [];

    /** @var array $services */
    protected $services = [];

    /**
     * ServiceRegister constructor.
     *
     * @param array|null $configs
     */
    public function __construct(array $configs = null)
    {
        $this->configs = $configs;
    }

    /**
     * @return \Micronative\ServiceSchema\Config\ServiceRegister
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exception\ConfigException
     */
    public function loadServices()
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
     * @param string|null $serviceName
     * @param string|null $schema
     * @param array|null $callbacks
     * @return \Micronative\ServiceSchema\Config\ServiceRegister
     */
    public function registerService(string $serviceName = null, string $schema = null, array $callbacks = null)
    {
        if (!isset($this->services[$serviceName])) {
            $this->services[$serviceName] = ['schema' => $schema, 'callbacks' => $callbacks];
        }

        return $this;
    }

    /**
     * @param string|null $serviceName
     * @return array|null
     */
    public function retrieveService(string $serviceName = null)
    {
        if (isset($this->services[$serviceName])) {
            return [$serviceName => $this->services[$serviceName]];
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
     * @param array $configs
     * @return \Micronative\ServiceSchema\Config\ServiceRegister
     */
    public function setConfigs(array $configs = null)
    {
        $this->configs = $configs;

        return $this;
    }

    /**
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param array $services
     * @return \Micronative\ServiceSchema\Config\ServiceRegister
     */
    public function setServices(array $services = null)
    {
        $this->services = $services;

        return $this;
    }

    /**
     * @param string|null $file
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    protected function loadFromJson(string $file = null)
    {
        $services = JsonReader::decode(JsonReader::read($file), true);
        $this->loadFromArray($services);
    }

    /**
     * @param string|null $file
     */
    protected function loadFromYaml(string $file = null)
    {
        $services = Yaml::parseFile($file);
        $this->loadFromArray($services);
    }

    /**
     * @param array|null $services
     */
    protected function loadFromArray(array $services = null){
        foreach ($services as $service) {
            if (isset($service['service']) && isset($service['schema'])) {
                $this->registerService(
                    $service['service'],
                    $service['schema'],
                    isset($service['callbacks']) ? $service['callbacks'] : null
                );
            }
        }
    }
}
