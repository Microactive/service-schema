<?php

namespace Micronative\ServiceSchema\Service;

use Micronative\ServiceSchema\Service\Exception\ServiceException;
use Psr\Container\ContainerInterface;

class ServiceFactory
{
    /**
     * @param string|null $serviceClass
     * @param string|null $schema
     * @param \Psr\Container\ContainerInterface|null $container
     * @return \Micronative\ServiceSchema\Service\ServiceInterface|false
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     */
    public function createService(string $serviceClass = null, string $schema = null, ContainerInterface $container = null)
    {
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
