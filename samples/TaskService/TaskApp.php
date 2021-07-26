<?php

namespace Samples\TaskService;

use Micronative\ServiceSchema\Processor;
use Samples\MessageBroker\MockBroker;
use Samples\TaskService\Broadcast\MockReceiver;
use Samples\TaskService\Events\TaskEvent;

class TaskApp
{
    /** @var \Samples\TaskService\Broadcast\MockReceiver */
    private $receiver;

    /** @var \Micronative\ServiceSchema\Processor */
    private $processor;

    /**
     * App constructor.
     * @param \Samples\MessageBroker\MockBroker|null $broker
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
    public function __construct(MockBroker $broker = null)
    {
        $this->receiver = new MockReceiver($broker);
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
