<?php

namespace Geekbrains\Leveltwo\Blog\Commands;

use Geekbrains\Leveltwo\Blog\Exceptions\ArgumentsException;
use Geekbrains\Leveltwo\Blog\Exceptions\CommandException;
use Geekbrains\Leveltwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\Leveltwo\Blog\User;
use Geekbrains\Leveltwo\Blog\UUID;
use Geekbrains\Leveltwo\Person\Name;
use Psr\Log\LoggerInterface;

class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface $logger
    ) {

    }
    // Вместо массива принимаем объект типа Arguments

    /**
     * @throws ArgumentsException
     * @throws CommandException
     */
    public function handle(Arguments $arguments): void
    {
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');
        if ($this->userExists($username)) {
            $this->logger->warning("User already exists: $username");
            throw new CommandException("User already exists: $username");
        }
        $uuid =  UUID::random();
        $this->usersRepository->save(new User(
            $uuid,
            $username,
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name'))
        ));
        $this->logger->info("User created: $uuid");
    }
    private function userExists(string $username): bool
    {
        try {
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }


}