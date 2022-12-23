<?php

namespace Geekbrains\Blog\UnitTests;

use Geekbrains\Leveltwo\Blog\Post;
use Geekbrains\Leveltwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Leveltwo\Blog\User;
use Geekbrains\Leveltwo\Blog\UUID;
use Geekbrains\Leveltwo\Person\Name;
use Monolog\Test\TestCase;
use PDO;
use PDOStatement;

class SqlitePostsRepositoryTest extends TestCase
{
    public function testItSavesPostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '93eb612e-96ab-4df9-85a0-1f216e825d69',
                ':author_uuid' => '133180cc-dfa7-4618-a2bf-8d8e648e0ed2',
                ':title' => 'Title 1',
                ':text' => 'Text 1'
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqlitePostsRepository($connectionStub, new DummyLogger());

        $user = new User(
            new UUID('133180cc-dfa7-4618-a2bf-8d8e648e0ed2'),
            'username',
            new Name('first', 'last')
        );

        $repository->save(
            new Post(
                new UUID('93eb612e-96ab-4df9-85a0-1f216e825d69'),
                $user,
                'Title 1',
                'Text 1'
            )
        );


    }
}