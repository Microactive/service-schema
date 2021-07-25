<?php

namespace Samples\UserService\Broadcast;

use Samples\MockBroker\MessageBroker;

class Publisher
{
    /** @var \Samples\MockBroker\MessageBroker */
    private $broker;

    public function __construct(MessageBroker $broker = null)
    {
        $this->broker = $broker;
    }

    public function publish(string $message)
    {
        $this->broker->push($message);
    }
}
