<?php

namespace Geekbrains\Leveltwo\Blog\Repositories\CommentsRepository;

use Geekbrains\Leveltwo\Blog\Comment;
use Geekbrains\Leveltwo\Blog\Exceptions\CommentNotFoundException;
use Geekbrains\Leveltwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Leveltwo\Blog\Exceptions\PostNotFoundException;
use Geekbrains\Leveltwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Leveltwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\Leveltwo\Blog\UUID;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    )
    {
    }

    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post_uuid, author_uuid, text) 
                    VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );
        $statement->execute([
            ':uuid' => (string)$comment->uuid(),
            ':post_uuid' => (string)$comment->post()->uuid(),
            ':author_uuid' => (string)$comment->author()->uuid(),
            ':text' => $comment->text()
        ]);

        $this->logger->info("Comment created: {$comment->uuid()}");
    }

    /**
     * @throws PostNotFoundException
     * @throws UserNotFoundException
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );
        $statement->execute([':uuid' => (string)$uuid]);
        return $this->getComment($statement, $uuid);
    }

    /**
     * @throws PostNotFoundException
     * @throws CommentNotFoundException
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    private function getComment(PDOStatement $statement, string $uuid): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if($result === false){
            $message = "Comment not found: $uuid";
            $this->logger->warning($message);
            throw new CommentNotFoundException($message);
        }
        $postsRepository = new SqlitePostsRepository($this->connection, $this->logger);
        $post = $postsRepository->get(new UUID($result['post_uuid']));

        $usersRepository = new SqliteUsersRepository($this->connection, $this->logger);
        $user = $usersRepository->get(new UUID($result['author_uuid']));

        return new Comment(
            new UUID($result['uuid']),
            $post,
            $user,
            $result['text']
        );
    }
    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM comments WHERE comments.uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

    }
}