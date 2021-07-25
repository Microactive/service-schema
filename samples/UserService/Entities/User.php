<?php

namespace Samples\UserService\Entities;

class User implements \JsonSerializable
{
    /** @var string */
    private $name;

    /** @var string */
    private $email;

    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * @return false|mixed|string
     */
    public function jsonSerialize()
    {
        return json_encode(['name' => $this->name, 'email' => $this->email]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \Samples\UserService\Entities\User
     */
    public function setName(string $name): User
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return \Samples\UserService\Entities\User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }
}
