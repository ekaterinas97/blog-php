<?php

namespace Geekbrains\Blog\UnitTests\Container;

use Geekbrains\Leveltwo\Blog\Container\DIContainer;
use Geekbrains\Leveltwo\Blog\Exceptions\NotFoundException;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\InMemoryUsersRepository;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DIContainerTest extends TestCase
{

    public function testItReturnsPredefinedObject(): void
    {
        $container = new DIContainer();

        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );
        $object = $container->get(SomeClassWithParameter::class);
        $this->assertInstanceOf(
            SomeClassWithParameter::class,
            $object
        );
    }
    public function testItResolesClassByContract(): void
    {
        $container = new DIContainer();

        $container->bind(
            UsersRepositoryInterface::class,
            InMemoryUsersRepository::class
        );
        $object = $container->get(UsersRepositoryInterface::class);

        $this->assertInstanceOf(InMemoryUsersRepository::class, $object);
    }
    public function testItResolvesClassWithoutDependencies(): void
    {
        $container = new DIContainer();
        $object = $container->get(SomeClassWithoutDependencies::class);

        $this->assertInstanceOf(
            SomeClassWithoutDependencies::class,
            $object
        );
    }
    public function testItThrowsAnExceptionIfCannotResolveType():void
    {
        $container = new DIContainer();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Cannot resolve type: Geekbrains\Blog\UnitTests\Container\SomeClass");

        $container->get(SomeClass::class);
    }
}