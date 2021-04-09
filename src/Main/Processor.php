<?php

namespace Micronative\ServiceSchema\Main;

use Micronative\ServiceSchema\Config\EventRegister;
use Micronative\ServiceSchema\Config\ServiceRegister;
use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Json\JsonReader;
use Micronative\ServiceSchema\Main\Exception\ProcessorException;
use Micronative\ServiceSchema\Service\Exception\ServiceException;
use Micronative\ServiceSchema\Service\RollbackInterface;
use Micronative\ServiceSchema\Service\ServiceFactory;
use Micronative\ServiceSchema\Service\ServiceInterface;
use Micronative\ServiceSchema\Service\ServiceValidator;
use Psr\Container\ContainerInterface;

class Processor implements ProcessorInterface
{
    /** @var \Micronative\ServiceSchema\Config\EventRegister */
    protected $eventRegister;

    /** @var \Micronative\ServiceSchema\Config\ServiceRegister */
    protected $serviceRegister;

    /** @var \Micronative\ServiceSchema\Service\ServiceFactory */
    protected $serviceFactory;

    /** @var \Micronative\ServiceSchema\Service\ServiceValidator */
    protected $serviceValidator;

    /** @var \Psr\Container\ContainerInterface */
    protected $container;

    /**
     * ServiceProvider constructor.
     *
     * @param array|null $eventConfigs
     * @param array|null $serviceConfigs
     * @param string|null $schemaDir
     * @param \Psr\Container\ContainerInterface|null $container
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exception\ConfigException
     */
    public function __construct(
        array $eventConfigs = null,
        array $serviceConfigs = null,
        string $schemaDir = null,
        ContainerInterface $container = null)
    {
        $this->eventRegister = new EventRegister($eventConfigs);
        $this->serviceRegister = new ServiceRegister($serviceConfigs);
        $this->serviceFactory = new ServiceFactory();
        $this->serviceValidator = new ServiceValidator(null, $schemaDir);
        $this->container = $container;
        $this->loadConfigs();
    }

    /**
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exception\ConfigException
     */
    protected function loadConfigs()
    {
        $this->eventRegister->loadEvents();
        $this->serviceRegister->loadServices();
    }

    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent|null $event
     * @param array|null $filteredEvents
     * @param bool $return if yes return first service result
     * @return bool
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     */
    public function process(AbstractEvent $event = null, array $filteredEvents = null, bool $return = false)
    {
        if (!empty($filteredEvents) && !in_array($event->getName(), $filteredEvents)) {
            throw new ProcessorException(ProcessorException::FILTERED_EVENT_ONLY . json_encode($filteredEvents));
        }

        $registeredEvents = $this->eventRegister->retrieveEvent($event->getName());
        if (empty($registeredEvents)) {
            throw new ProcessorException(ProcessorException::NO_REGISTER_EVENTS . $event->getName());
        }

        foreach ($registeredEvents as $eventName => $serviceNames) {
            if (empty($serviceNames)) {
                throw new ProcessorException(ProcessorException::NO_REGISTER_SERVICES . $event->getName());
            }

            foreach ($serviceNames as $serviceName) {
                $registeredService = $this->serviceRegister->retrieveService($serviceName);
                if (empty($registeredService)) {
                    continue;
                }

                $jsonSchema = $registeredService[$serviceName]['schema'];
                $callbacks = $registeredService[$serviceName]['callbacks'];
                $service = $this->serviceFactory->createService($serviceName, $jsonSchema, $this->container);
                if (empty($service)) {
                    continue;
                }

                if ($return === true) {
                    return $this->runService($event, $service, $callbacks, $return);
                }

                $this->runService($event, $service, $callbacks);
            }
        }

        return true;
    }

    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent|null $event
     * @param \Micronative\ServiceSchema\Service\ServiceInterface|null $service
     * @param array|null $callbacks
     * @param bool $return
     * @return \Micronative\ServiceSchema\Event\AbstractEvent|bool
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     */
    public function runService(AbstractEvent $event = null, ServiceInterface $service = null, array $callbacks = null, bool $return = false)
    {
        $json = JsonReader::decode($event->toJson());
        $validator = $this->serviceValidator->validate($json, $service);
        if (!$validator->isValid()) {
            throw  new ServiceException(sprintf(ServiceException::INVALIDATED_JSON_STRING, $service->getJsonSchema(), json_encode($validator->getErrors())));
        }

        if (isset($json->payload)) {
            $event->setPayload($json->payload);
        }

        $result = $service->consume($event);
        if ($return === true) {
            return $result;
        }

        if (($result instanceof AbstractEvent) && !empty($callbacks)) {
            return $this->runCallbacks($result, $callbacks);
        }

        return $result;
    }

    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent|null $event
     * @param array|null $callbacks
     * @return bool
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     */
    public function runCallbacks(AbstractEvent $event = null, array $callbacks = null)
    {
        if (empty($callbacks)) {
            return true;
        }

        foreach ($callbacks as $callback) {
            $service = $this->serviceFactory->createService($callback, null, $this->container);
            if (empty($service)) {
                continue;
            }

            $service->consume($event);
        }

        return true;
    }

    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent|null $event
     * @return bool
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     */
    public function rollback(AbstractEvent $event = null)
    {
        $registeredEvents = $this->eventRegister->retrieveEvent($event->getName());
        if (empty($registeredEvents)) {
            throw new ProcessorException(ProcessorException::NO_REGISTER_EVENTS . $event->getName());
        }

        foreach ($registeredEvents as $eventName => $services) {
            if (empty($services)) {
                throw new ProcessorException(ProcessorException::NO_REGISTER_SERVICES . $event->getName());
            }

            foreach ($services as $serviceName) {
                $registerService = $this->serviceRegister->retrieveService($serviceName);
                if (empty($registerService)) {
                    continue;
                }

                $jsonSchema = $registerService[$serviceName]['schema'];
                $service = $this->serviceFactory->createService($serviceName, $jsonSchema, $this->container);
                if (empty($service)) {
                    continue;
                }

                if ($service instanceof RollbackInterface) {
                    $this->rollbackService($event, $service);
                }
            }
        }

        return true;
    }

    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent|null $event
     * @param \Micronative\ServiceSchema\Service\RollbackInterface|null $service
     * @return \Micronative\ServiceSchema\Event\AbstractEvent|bool
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     */
    public function rollbackService(AbstractEvent $event = null, RollbackInterface $service = null)
    {
        $json = JsonReader::decode($event->toJson());
        $validator = $this->serviceValidator->validate($json, $service);
        if (!$validator->isValid()) {
            throw  new ServiceException(sprintf(ServiceException::INVALIDATED_JSON_STRING, $service->getJsonSchema(), json_encode($validator->getErrors())));
        }

        if (isset($json->payload)) {
            $event->setPayload($json->payload);
        }

        return $service->rollback($event);
    }

    /**
     * @return \Micronative\ServiceSchema\Config\EventRegister
     */
    public function getEventRegister()
    {
        return $this->eventRegister;
    }

    /**
     * @param \Micronative\ServiceSchema\Config\EventRegister|null $eventRegister
     * @return \Micronative\ServiceSchema\Main\Processor
     */
    public function setEventRegister(EventRegister $eventRegister = null)
    {
        $this->eventRegister = $eventRegister;

        return $this;
    }

    /**
     * @return \Micronative\ServiceSchema\Config\ServiceRegister
     */
    public function getServiceRegister()
    {
        return $this->serviceRegister;
    }

    /**
     * @param \Micronative\ServiceSchema\Config\ServiceRegister|null $serviceRegister
     * @return \Micronative\ServiceSchema\Main\Processor
     */
    public function setServiceRegister(ServiceRegister $serviceRegister = null)
    {
        $this->serviceRegister = $serviceRegister;

        return $this;
    }

    /**
     * @return \Micronative\ServiceSchema\Service\ServiceFactory
     */
    public function getServiceFactory()
    {
        return $this->serviceFactory;
    }

    /**
     * @param \Micronative\ServiceSchema\Service\ServiceFactory|null $serviceFactory
     * @return \Micronative\ServiceSchema\Main\Processor
     */
    public function setServiceFactory(ServiceFactory $serviceFactory = null)
    {
        $this->serviceFactory = $serviceFactory;

        return $this;
    }

    /**
     * @return \Micronative\ServiceSchema\Service\ServiceValidator
     */
    public function getServiceValidator()
    {
        return $this->serviceValidator;
    }

    /**
     * @param \Micronative\ServiceSchema\Service\ServiceValidator|null $serviceValidator
     * @return \Micronative\ServiceSchema\Main\Processor
     */
    public function setServiceValidator(ServiceValidator $serviceValidator = null)
    {
        $this->serviceValidator = $serviceValidator;

        return $this;
    }

    /**
     * @return \Psr\Container\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param \Psr\Container\ContainerInterface|null $container
     * @return Processor
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        return $this;
    }

}
