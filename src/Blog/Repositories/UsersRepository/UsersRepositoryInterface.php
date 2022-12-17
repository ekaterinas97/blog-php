<?php

namespace Geekbrains\Leveltwo\Blog\Repositories\UsersRepository;

use Geekbrains\Leveltwo\Blog\User;
use Geekbrains\Leveltwo\Blog\UUID;

interface UsersRepositoryInterface
{
    public function save(User $user): void;

    public function get(UUID $uuid): User;

    public function getByUsername(string $username): User;
}