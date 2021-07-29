<?php

namespace Samples\UserService;

use Micronative\ServiceSchema\Processor;
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

    /** @var \Micronative\ServiceSchema\Processor */
    private $processor;

    /**
     * UserApp constructor.
     * @param \Samples\MessageBroker\MockBroker|null $broker
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
    public function __construct(MockBroker $broker = null)
    {
        $this->publisher = new MockPublisher($broker);
        $this->userRepository = new UserRepository();
        $assetDir = dirname(__FILE__);
        $this->processor = new Processor(null, null, $assetDir);
    }

    /**
     * @param string $name
     * @param string $email
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function createUser(string $name, string $email)
    {
        $user = new User($name, $email);
        if ($this->userRepository->save($user)) {
            $userEvent = new UserEvent(UserRepository::USER_CREATED, Uuid::uuid4()->toString(), $user->toArray());
            /**
             * Validate the event against the event schema before publishing to message broker
             * These event event schemas are the contracts between publisher and consumer
             * These event event schemas might be published as part of the open api specification
             */
            if ($this->processor->validate($userEvent, true)) {
                $this->publisher->publish($userEvent->jsonSerialize());
            }
        }
    }

    /**
     * @param \Samples\UserService\Entities\User $user
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function updateUser(User $user)
    {
        if ($this->userRepository->update($user)) {
            $userEvent = new UserEvent(UserRepository::USER_UPDATED, Uuid::uuid4()->toString(), $user->toArray());
            /**
             * Validate the event against the event schema before publishing to message broker
             * These event event schemas are the contracts between publisher and consumer
             * These event event schemas might be published as part of the open api specification
             */
            $userEvent->setSchema('/assets/schemas/events/User.json');
            if ($this->processor->validate($userEvent, true)) {
                $this->publisher->publish($userEvent->jsonSerialize());
            }
        }
    }
}
