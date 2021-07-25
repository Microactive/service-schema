<?php

namespace Samples\Services;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\AbstractService;
use Micronative\ServiceSchema\Service\ServiceInterface;

class SendNotificationToNewUser extends AbstractService implements ServiceInterface
{
    public function consume(AbstractEvent $event = null)
    {
        return 'Notification has been sent to new user.';
    }
}
