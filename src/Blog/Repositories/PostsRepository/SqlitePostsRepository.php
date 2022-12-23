<?php

namespace Geekbrains\Leveltwo\Blog\Repositories\PostsRepository;

use Geekbrains\Leveltwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Leveltwo\Blog\Exceptions\PostNotFoundException;
use Geekbrains\Leveltwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Leveltwo\Blog\Post;
use Geekbrains\Leveltwo\Blog\User;
use Geekbrains\Leveltwo\Blog\UUID;
use Geekbrains\Leveltwo\Person\Name;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqlitePostsRepository implements PostsRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    )
    {
    }

    public function save(Post $post): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author_uuid, title, text) 
                    VALUES (:uuid, :author_uuid, :title, :text)'
        );
        $statement->execute([
            ':uuid' => (string)$post->uuid(),
            ':author_uuid' => (string)$post->author()->uuid(),
            ':title' => $post->title(),
            ':text' => $post->text()
        ]);

        $this->logger->info("Post created: {$post->uuid()}");
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
//            'SELECT * FROM posts WHERE uuid = :uuid'
            'SELECT posts.uuid, posts.author_uuid,
                           posts.title, posts.text,
                           users.first_name, users.last_name, users.username
                    FROM users LEFT JOIN posts
                    ON users.uuid=posts.author_uuid
                    WHERE posts.uuid = :uuid'
        );

        $statement->execute([':uuid' => (string)$uuid]);
        return $this->getPost($statement, $uuid);
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidArgumentException
     */
    private function getPost(PDOStatement $statement, string $uuid): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if($result === false){
            $message = "Post not found: $uuid";
            $this->logger->warning($message);
            throw new PostNotFoundException($message);
        }

        return new Post(
            new UUID($result['uuid']),
            new User(
                new UUID($result['author_uuid']),
                $result['username'],
                new Name($result['first_name'], $result['last_name'])
            ),
            $result['title'],
            $result['text']
        );
    }

    public function delete(UUID $uuid):void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM posts WHERE posts.uuid=:uuid'
        );
        $statement->execute(['uuid' => $uuid]);
    }
}