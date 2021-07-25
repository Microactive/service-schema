<?php

namespace Samples\TaskService\Broadcast;

use Samples\MockBroker\MessageBroker;

class Receiver
{
    /** @var \Samples\MockBroker\MessageBroker */
    private $broker;

    public function __construct(MessageBroker $broker = null)
    {
        $this->broker = $broker;
    }

    public function get()
    {
        return $this->broker->shift();
    }
}
