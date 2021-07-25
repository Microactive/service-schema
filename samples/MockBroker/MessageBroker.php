<?php

namespace Samples\MockBroker;

class MessageBroker
{
    /** @var string[] */
    private $messages = [];

    /**
     * @param string $message
     */
    public function push(string $message)
    {
        array_push($this->messages, $message);
    }

    /**
     * @return string
     */
    public function shift()
    {
        return array_shift($this->messages);
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param string[] $messages
     * @return \Samples\MockBroker\MessageBroker
     */
    public function setMessages(array $messages): MessageBroker
    {
        $this->messages = $messages;

        return $this;
    }
}
