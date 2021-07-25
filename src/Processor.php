<?php

namespace Micronative\ServiceSchema;

use Micronative\ServiceSchema\Command\ConsumeCommand;
use Micronative\ServiceSchema\Command\RollbackCommand;
use Micronative\ServiceSchema\Config\EventConfigRegister;
use Micronative\ServiceSchema\Config\ServiceConfigRegister;
use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Exceptions\ProcessorException;
use Micronative\ServiceSchema\Service\RollbackInterface;
use Micronative\ServiceSchema\Service\ServiceFactory;
use Micronative\ServiceSchema\Service\ServiceInterface;
use Micronative\ServiceSchema\Service\ServiceValidator;
use Psr\Container\ContainerInterface;

class Processor implements ProcessorInterface
{
    /** @var \Micronative\ServiceSchema\Config\EventConfigRegister */
    protected $eventConfigRegister;

    /** @var \Micronative\ServiceSchema\Config\ServiceConfigRegister */
    protected $serviceConfigRegister;

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
     * @param string|null $schemaDir a relative dir from where the schemas are stored
     * @param \Psr\Container\ContainerInterface|null $container
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function __construct(
        array $eventConfigs = null,
        array $serviceConfigs = null,
        string $schemaDir = null,
        ContainerInterface $container = null
    ) {
        $this->eventConfigRegister = new EventConfigRegister($eventConfigs);
        $this->serviceConfigRegister = new ServiceConfigRegister($serviceConfigs);
        $this->serviceFactory = new ServiceFactory();
        $this->serviceValidator = new ServiceValidator($schemaDir);
        $this->container = $container;
        $this->loadConfigs();
    }

    /**
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
    protected function loadConfigs()
    {
        $this->eventConfigRegister->loadEventConfigs();
        $this->serviceConfigRegister->loadServiceConfigs();
    }

    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent|null $event
     * @param array|null $filteredEvents
     * @param bool $return if yes return first service result
     * @return bool
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     */
    public function process(AbstractEvent $event = null, array $filteredEvents = null, bool $return = false)
    {
        $this->checkFilteredEvents($event, $filteredEvents);
        $serviceClasses = $this->retrieveServiceClasses($event);
        foreach ($serviceClasses as $class) {
            if (empty($serviceConfig = $this->serviceConfigRegister->retrieveServiceConfig($class))) {
                continue;
            }

            if (empty($service = $this->serviceFactory->createService($serviceConfig, $this->container))) {
                continue;
            }

            $callbacks = $serviceConfig->getCallbacks();
            if ($return === true) {
                return $this->runService($event, $service, $callbacks, $return);
            }

            $this->runService($event, $service, $callbacks);
        }

        return true;
    }

    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent $event
     * @return bool
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     */
    public function rollback(AbstractEvent $event)
    {
        $serviceClasses = $this->retrieveServiceClasses($event);
        foreach ($serviceClasses as $class) {
            if (empty($serviceConfig = $this->serviceConfigRegister->retrieveServiceConfig($class))) {
                continue;
            }

            if (empty($service = $this->serviceFactory->createService($serviceConfig, $this->container))) {
                continue;
            }

            if ($service instanceof RollbackInterface) {
                $this->rollbackService($event, $service);
            }
        }

        return true;
    }

    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent $event
     * @param array|null $filteredEvents
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    private function checkFilteredEvents(AbstractEvent $event, array $filteredEvents = null)
    {
        if (!empty($filteredEvents) && !in_array($event->getName(), $filteredEvents)) {
            throw new ProcessorException(ProcessorException::FILTERED_EVENT_ONLY . json_encode($filteredEvents));
        }
    }

    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent $event
     * @return string[]
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    private function retrieveServiceClasses(AbstractEvent $event)
    {
        $eventConfig = $this->eventConfigRegister->retrieveEventConfig($event->getName());
        if (empty($eventConfig)) {
            throw new ProcessorException(ProcessorException::NO_REGISTER_EVENTS . $event->getName());
        }

        if (empty($serviceClasses = $eventConfig->getServiceClasses())) {
            throw new ProcessorException(ProcessorException::NO_REGISTER_SERVICES . $event->getName());
        }

        return $serviceClasses;
    }

    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent|null $event
     * @param \Micronative\ServiceSchema\Service\ServiceInterface|null $service
     * @param array|null $callbacks
     * @param bool $return
     * @return \Micronative\ServiceSchema\Event\AbstractEvent|bool
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     */
    private function runService(
        AbstractEvent $event,
        ServiceInterface $service,
        array $callbacks = null,
        bool $return = false
    ) {
        $consumeCommand = new ConsumeCommand($this->serviceValidator, $service, $event);
        $result = $consumeCommand->execute();
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
     * @param \Micronative\ServiceSchema\Service\RollbackInterface|null $service
     * @return \Micronative\ServiceSchema\Event\AbstractEvent|bool
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     */
    private function rollbackService(AbstractEvent $event, RollbackInterface $service)
    {
        $rollbackCommand = new RollbackCommand($this->serviceValidator, $service, $event);

        return $rollbackCommand->execute();
    }

    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent $event
     * @param array $callbacks
     * @return bool
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     */
    private function runCallbacks(AbstractEvent $event, array $callbacks)
    {
        foreach ($callbacks as $class) {
            if (empty($serviceConfig = $this->serviceConfigRegister->retrieveServiceConfig($class))) {
                continue;
            }

            if ($service = $this->serviceFactory->createService($serviceConfig, $this->container)) {
                continue;
            }
            $consumeCommand = new ConsumeCommand($this->serviceValidator, $service, $event);
            $consumeCommand->execute();
        }

        return true;
    }

    /**
     * @return \Micronative\ServiceSchema\Config\EventConfigRegister
     */
    public function getEventConfigRegister()
    {
        return $this->eventConfigRegister;
    }

    /**
     * @param \Micronative\ServiceSchema\Config\EventConfigRegister|null $eventConfigRegister
     * @return \Micronative\ServiceSchema\Processor
     */
    public function setEventConfigRegister(EventConfigRegister $eventConfigRegister = null)
    {
        $this->eventConfigRegister = $eventConfigRegister;

        return $this;
    }

    /**
     * @return \Micronative\ServiceSchema\Config\ServiceConfigRegister
     */
    public function getServiceConfigRegister()
    {
        return $this->serviceConfigRegister;
    }

    /**
     * @param \Micronative\ServiceSchema\Config\ServiceConfigRegister|null $serviceConfigRegister
     * @return \Micronative\ServiceSchema\Processor
     */
    public function setServiceConfigRegister(ServiceConfigRegister $serviceConfigRegister = null)
    {
        $this->serviceConfigRegister = $serviceConfigRegister;

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
     * @return \Micronative\ServiceSchema\Processor
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
     * @return \Micronative\ServiceSchema\Processor
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
