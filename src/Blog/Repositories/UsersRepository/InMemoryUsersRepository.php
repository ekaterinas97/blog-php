<?php

namespace Geekbrains\Leveltwo\Blog\Repositories\UsersRepository;

use Geekbrains\Leveltwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Leveltwo\Blog\User;
use Geekbrains\Leveltwo\Blog\UUID;

class InMemoryUsersRepository implements UsersRepositoryInterface
{
    private array $users = [];

    /**
     * @param User $user
     * @return void
     */
    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    /**
     * @param UUID $id
     * @return User
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User
    {
        foreach ($this->users as $user){
            if((string)$user->uuid() === (string)$uuid){
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $uuid");
    }

    /**
     * @throws UserNotFoundException
     */
    public function getByUsername(string $username): User
    {
        foreach ($this->users as $user){
            if($user->username() === $username){
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $username");
    }
    public function checkUserAlreadyExists(string $username): void
    {
        // TODO: Implement checkUserAlreadyExists() method.
    }
}