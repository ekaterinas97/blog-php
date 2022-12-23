<?php

namespace Geekbrains\Leveltwo\Blog\Repositories\LikesRepository;

use Geekbrains\Leveltwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Leveltwo\Blog\Exceptions\LikeAlreadyExists;
use Geekbrains\Leveltwo\Blog\Exceptions\LikesNotFoundException;
use Geekbrains\Leveltwo\Blog\Exceptions\PostNotFoundException;
use Geekbrains\Leveltwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Leveltwo\Blog\Likes\PostLike;
use Geekbrains\Leveltwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\Leveltwo\Blog\UUID;
use PDO;

class SqlitePostsLikesRepository implements PostsLikesRepositoryInterface
{
    public function __construct(
        private PDO $connection
    )
    {
    }

    public function save(PostLike $like): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO postsLikes (uuid, user_uuid, post_uuid)
                    VALUES (:uuid, :user_uuid, :post_uuid)'
        );
        $statement->execute([
            ':uuid' => (string)$like->uuid(),
            ':user_uuid' => (string)$like->user()->uuid(),
            ':post_uuid' => (string)$like->post()->uuid()
        ]);
    }

    /**
     * @throws LikesNotFoundException
     * @throws PostNotFoundException
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function getByPostUuid(UUID $uuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM postsLikes WHERE post_uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new LikesNotFoundException(
                "No likes to post with uuid: $uuid"
            );
        }
        $likes = [];
        $postsRepository = new SqlitePostsRepository($this->connection);
        $usersRepository = new SqliteUsersRepository($this->connection);

        foreach ($result as $like){
            $post = $postsRepository->get(new UUID($like['post_uuid']));
            $user = $usersRepository->get(new UUID($like['user_uuid']));
            $likes[] = new PostLike(
                uuid: new UUID($like['uuid']),
                user: $user,
                post: $post
            );
        }

        return $likes;
    }

    /**
     * @throws LikeAlreadyExists
     */
    public function checkUserLikeForPostExists(UUID $postUuid, UUID $userUuid): void
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM postsLikes WHERE post_uuid = :postUuid AND user_uuid = :userUuid'
        );
        $statement->execute([
            ':postUuid' => (string)$postUuid,
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