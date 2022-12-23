<?php

namespace Geekbrains\Blog\UnitTests\Commands;

use Geekbrains\Blog\UnitTests\DummyLogger;
use Geekbrains\Leveltwo\Blog\Commands\Arguments;
use Geekbrains\Leveltwo\Blog\Commands\CreateUserCommand;
use Geekbrains\Leveltwo\Blog\Exceptions\CommandException;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\DummyUsersRepository;
use Monolog\Test\TestCase;

class CreateUserCommandTest extends TestCase
{
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        $command = new CreateUserCommand(new DummyUsersRepository() , new DummyLogger());

        $this->expectException(CommandException::class);

        $this->expectExceptionMessage("User already exists: Luke");

        $command->handle(new Arguments(['username' => 'Luke']));
    }

}