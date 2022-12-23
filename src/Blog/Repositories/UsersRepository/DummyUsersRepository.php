<?php

namespace Geekbrains\Leveltwo\Blog\Repositories\UsersRepository;

use Geekbrains\Leveltwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Leveltwo\Blog\User;
use Geekbrains\Leveltwo\Blog\UUID;
use Geekbrains\Leveltwo\Person\Name;

class DummyUsersRepository implements UsersRepositoryInterface
{
    public function save(User $user): void
    {
        // TODO: Implement save() method.
    }
    public function get(UUID $uuid): User
    {
        // TODO: Implement get() method.
        throw new UserNotFoundException("Not Found");
    }

    public function getByUsername(string $username): User
    {
        return new User(UUID::random(), 'user456', new Name('first', '
        last'));
    }

    public function checkUserAlreadyExists(string $username): void
    {
        // TODO: Implement checkUserAlreadyExists() method.
    }
}