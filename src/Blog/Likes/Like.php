<?php

namespace Geekbrains\Leveltwo\Blog\Likes;

use Geekbrains\Leveltwo\Blog\User;
use Geekbrains\Leveltwo\Blog\UUID;

class Like
{
    public function __construct(
        private UUID $uuid,
        private User $user
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
     * @return User
     */
    public function user(): User
    {
        return $this->user;
    }




}