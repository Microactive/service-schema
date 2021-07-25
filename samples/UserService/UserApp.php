<?php

namespace Samples\UserService;

use Ramsey\Uuid\Uuid;
use Samples\MockBroker\MessageBroker;
use Samples\UserService\Broadcast\Publisher;
use Samples\UserService\Entities\User;
use Samples\UserService\Events\UserEvent;
use Samples\UserService\Repositories\UserRepository;

class UserApp
{
    /** @var \Samples\UserService\Broadcast\Publisher */
    private $publisher;

    /** @var \Samples\UserService\Repositories\UserRepository */
    private $userRepository;

    /**
     * App constructor.
     * @param \Samples\MockBroker\MessageBroker|null $broker
     */
    public function __construct(MessageBroker $broker = null)
    {
        $this->publisher = new Publisher($broker);
        $this->userRepository = new UserRepository();
    }

    public function createUser(string $name, string $email)
    {
        $user = new User($name, $email);
        if ($this->userRepository->save($user)) {
            $userEvent = new UserEvent(UserRepository::USER_CREATED, Uuid::uuid4()->toString(), $user->jsonSerialize());
            $this->publisher->publish($userEvent->toJson());
        }
    }
}
