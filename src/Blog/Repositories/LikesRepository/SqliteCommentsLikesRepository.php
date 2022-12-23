<?php

namespace Geekbrains\Leveltwo\Blog\Repositories\LikesRepository;

use Geekbrains\Leveltwo\Blog\Exceptions\CommentLikesNotFound;
use Geekbrains\Leveltwo\Blog\Exceptions\CommentNotFoundException;
use Geekbrains\Leveltwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Leveltwo\Blog\Exceptions\LikeAlreadyExists;
use Geekbrains\Leveltwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Leveltwo\Blog\Likes\CommentLike;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\Leveltwo\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use Geekbrains\Leveltwo\Blog\UUID;
use PDO;

class SqliteCommentsLikesRepository implements CommentsLikesRepositoryInterface
{
    public function __construct(
        private PDO $connection
    )
    {
    }

    public function save(CommentLike $like): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO commentsLikes (uuid, comment_uuid, user_uuid)
                    VALUES (:uuid, :comment_uuid, :user_uuid)'
        );
        $statement->execute([
            ':uuid' => (string)$like->uuid(),
            ':comment_uuid' => (string)$like->comment()->uuid(),
            ':user_uuid' => (string)$like->user()->uuid(),
        ]);
    }

    /**
     * @throws CommentLikesNotFound
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     * @throws CommentNotFoundException
     */
    public function getByCommentUuid(UUID $uuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM commentsLikes WHERE comment_uuid = :uuid'
        );
        $statement->execute([':uuid' => (string)$uuid]);

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new CommentLikesNotFound(
                "No likes to comment with uuid: $uuid"
            );
        }

        $commentLikes = [];
        $commentsRepository = new SqliteCommentsRepository($this->connection);
        $usersRepository = new SqliteUsersRepository($this->connection);

        foreach ($result as $like){
            $comment = $commentsRepository->get(new UUID($like['comment_uuid']));
            $user = $usersRepository->get(new UUID($like['user_uuid']));
            $commentLikes[] = new CommentLike(
                uuid: new UUID($like['uuid']),
                user: $user,
                comment: $comment
            );
        }
        return $commentLikes;
    }

    /**
     * @throws LikeAlreadyExists
     */
    public function checkUserLikeForCommentExists(UUID $commentUuid, UUID $userUuid): void
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM commentsLikes WHERE comment_uuid = :commentUuid AND user_uuid = :userUuid'
        );
        $statement->execute([
            ':commentUuid' => (string)$commentUuid,
            ':userUuid' => (string)$userUuid
        ]);

        $isExist = $statement->fetch(PDO::FETCH_ASSOC);

        if($isExist){
            throw new LikeAlreadyExists(
                "Like already exists"
            );
        }
    }

}