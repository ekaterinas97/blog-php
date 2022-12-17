<?php

namespace Geekbrains\Leveltwo\Blog;

use Geekbrains\Leveltwo\Person\Name;

class User
{
    public function __construct(
        private UUID $uuid,
        private string $username,
        private Name $name
    )
    {
    }

    /**
     * @return UUID
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function username(): string
    {
        return $this->username;
    }

    /**
     * @return Name
     */
    public function name(): Name
    {
        return $this->name;
    }
    public function __toString(): string
    {
        return "User: $this->name";
    }

}