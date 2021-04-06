<?php

namespace Micronative\ServiceSchema\Tests\Event;

use Micronative\ServiceSchema\Event\AbstractEvent;

class SampleInvalidEvent extends AbstractEvent
{
    /**
     * @return false|string
     */
    public function toJson()
    {
        return 'invalid_json';
    }

    /**
     * @param array|null $data
     * @return \Micronative\ServiceSchema\Event\AbstractEvent|void
     */
    public function setData(array $data = null)
    {
        $this->id = isset($data['id']) ? $data['id'] : null;
        $this->name = isset($data['name']) ? $data['name'] : null;
        $this->payload = isset($data['payload']) ? $data['payload'] : null;
    }
}
