<?php

namespace Geekbrains\Leveltwo\Blog\Repositories\CommentsRepository;

use Geekbrains\Leveltwo\Blog\Comment;
use Geekbrains\Leveltwo\Blog\UUID;
use PDO;

class SqlitePostsRepository implements CommentsRepositoryInterface
{
    public function __construct(
        private PDO $connection
    )
    {
    }

    public function save(Comment $comment): void
    {
        // TODO: Implement save() method.
    }
    public function get(UUID $uuid): Comment
    {
        // TODO: Implement get() method.
    }
}