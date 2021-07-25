<?php

namespace Samples\TaskService;

use Micronative\ServiceSchema\Processor;
use Samples\MockBroker\MessageBroker;
use Samples\TaskService\Broadcast\Receiver;
use Samples\TaskService\Events\TaskEvent;

class TaskApp
{
    /** @var \Samples\TaskService\Broadcast\Receiver */
    private $receiver;

    /** @var \Micronative\ServiceSchema\Processor */
    private $processor;

    /**
     * App constructor.
     * @param \Samples\MockBroker\MessageBroker|null $broker
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
    public function __construct(MessageBroker $broker = null)
    {
        $this->receiver = new Receiver($broker);
        $assetDir = dirname(__FILE__);
        $this->processor = new Processor(
            [$assetDir . "/assets/configs/events.yml"],
            [$assetDir . "/assets/configs/services.yml"],
            $assetDir
        );
    }

    /**
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     */
    public function listen()
    {
        $message = $this->receiver->get();
        if (!empty($message)) {
            $obj = json_decode($message);
            $taskEvent = new TaskEvent($obj->name, $obj->id, $obj->payload);
            $this->processor->process($taskEvent);
        }
    }
}
