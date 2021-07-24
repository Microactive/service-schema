<?php

namespace Micronative\ServiceSchema\Service;

use Micronative\ServiceSchema\Config\ServiceConfig;
use Micronative\ServiceSchema\Service\Exceptions\ServiceException;
use Psr\Container\ContainerInterface;

class ServiceFactory
{
    /**
     * @param \Micronative\ServiceSchema\Config\ServiceConfig $serviceConfig
     * @param \Psr\Container\ContainerInterface|null $container
     * @return false|\Micronative\ServiceSchema\Service\ServiceInterface
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     */
    public function createService(ServiceConfig $serviceConfig, ContainerInterface $container = null)
    {
        $serviceClass = $serviceConfig->getClass();
        $schema = $serviceConfig->getSchema();
        try {
            $service = new $serviceClass($container);
        } catch (\Error $exception) {
            throw new ServiceException(ServiceException::INVALID_SERVICE_CLASS . $serviceClass);
        }

        if ($service instanceof ServiceInterface) {
            $service->setName($serviceClass);
            $service->setJsonSchema($schema);

            return $service;
        }

        return false;
    }
}
