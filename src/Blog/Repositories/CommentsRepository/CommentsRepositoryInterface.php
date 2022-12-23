<?php

namespace Geekbrains\Leveltwo\Blog\Repositories\CommentsRepository;

use Geekbrains\Leveltwo\Blog\Comment;
use Geekbrains\Leveltwo\Blog\UUID;

interface CommentsRepositoryInterface
{
    public function save(Comment $comment): void;

    public function get(UUID $uuid): Comment;

    public function delete(UUID $uuid): void;

}