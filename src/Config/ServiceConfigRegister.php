<?php

namespace Micronative\ServiceSchema\Config;

use Micronative\ServiceSchema\Config\Exceptions\ConfigException;
use Micronative\ServiceSchema\Json\JsonReader;
use Symfony\Component\Yaml\Yaml;

class ServiceConfigRegister
{
    /** @var string[] $configFiles */
    protected $configFiles = [];

    /** @var \Micronative\ServiceSchema\Config\ServiceConfig[] $serviceConfigs */
    protected $serviceConfigs = [];

    /** @var \Micronative\ServiceSchema\Config\ServiceConfig[] $aliasConfigs */
    protected $aliasConfigs = [];

    /**
     * ServiceRegister constructor.
     *
     * @param array|null $files
     */
    public function __construct(array $files = null)
    {
        $this->configFiles = $files;
    }

    /**
     * @return \Micronative\ServiceSchema\Config\ServiceConfigRegister
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function loadServiceConfigs()
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
     * @param string $serviceClass
     * @return \Micronative\ServiceSchema\Config\ServiceConfig|null
     */
    public function retrieveServiceConfig(string $serviceClass)
    {
        if (isset($this->serviceConfigs[$serviceClass])) {
            return $this->serviceConfigs[$serviceClass];
        }

        if(isset($this->aliasConfigs[$serviceClass])){
            return $this->aliasConfigs[$serviceClass];
        }

        return null;
    }

    /**
     * @param \Micronative\ServiceSchema\Config\ServiceConfig $serviceConfig
     * @return \Micronative\ServiceSchema\Config\ServiceConfigRegister
     */
    public function registerServiceConfig(ServiceConfig $serviceConfig)
    {
        $serviceClass = $serviceConfig->getClass();
        $this->serviceConfigs[$serviceClass] = $serviceConfig;

        if(!empty($serviceAlias = $serviceConfig->getAlias())){
            $this->aliasConfigs[$serviceAlias] = $serviceConfig;
        }

        return $this;
    }

    /**
     * @param string|null $file
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
    private function loadFromJson(string $file = null)
    {
        $services = JsonReader::decode(JsonReader::read($file), true);
        $this->loadFromArray($services);
    }

    /**
     * @param string|null $file
     */
    private function loadFromYaml(string $file = null)
    {
        $services = Yaml::parseFile($file);
        $this->loadFromArray($services);
    }

    /**
     * @param array|null $services
     */
    private function loadFromArray(array $services = null)
    {
        foreach ($services as $service) {
            if (isset($service['service'])) {
                $class = $service['service'];
                $alias = isset($service['alias']) ? $service['alias'] : null;
                $schema = isset($service['schema']) ? $service['schema'] : null;
                $callbacks = isset($service['callbacks']) ? $service['callbacks'] : null;
                $serviceConfig = new ServiceConfig($class, $alias, $schema, $callbacks);
                $this->registerServiceConfig($serviceConfig);
            }
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
     * @param string[] $configFiles
     * @return \Micronative\ServiceSchema\Config\ServiceConfigRegister
     */
    public function setConfigFiles(array $configFiles = null)
    {
        $this->configFiles = $configFiles;

        return $this;
    }

    /**
     * @return array
     */
    public function getServiceConfigs()
    {
        return $this->serviceConfigs;
    }

    /**
     * @param array|null $serviceConfigs
     * @return \Micronative\ServiceSchema\Config\ServiceConfigRegister
     */
    public function setServiceConfigs(array $serviceConfigs = null)
    {
        $this->serviceConfigs = $serviceConfigs;

        return $this;
    }
}
