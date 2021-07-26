<?php

namespace Samples\UserService;

use Ramsey\Uuid\Uuid;
use Samples\MessageBroker\MockBroker;
use Samples\UserService\Broadcast\MockPublisher;
use Samples\UserService\Entities\User;
use Samples\UserService\Events\UserEvent;
use Samples\UserService\Repositories\UserRepository;

class UserApp
{
    /** @var \Samples\UserService\Broadcast\MockPublisher */
    private $publisher;

    /** @var \Samples\UserService\Repositories\UserRepository */
    private $userRepository;

    /**
     * App constructor.
     * @param \Samples\MessageBroker\MockBroker|null $broker
     */
    public function __construct(MockBroker $broker = null)
    {
        $this->publisher = new MockPublisher($broker);
        $this->userRepository = new UserRepository();
    }

    public function createUser(string $name, string $email)
    {
        $user = new User($name, $email);
        if ($this->userRepository->save($user)) {
            $userEvent = new UserEvent(UserRepository::USER_CREATED, Uuid::uuid4()->toString(), $user->toArray());
            $this->publisher->publish($userEvent->toJson());
        }
    }
}
