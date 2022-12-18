<?php

namespace Geekbrains\Leveltwo\Blog\Repositories\PostsRepository;

use Geekbrains\Leveltwo\Blog\Post;
use Geekbrains\Leveltwo\Blog\UUID;


interface PostsRepositoryInterface
{
    public function save(Post $post): void;

    public function get(UUID $uuid): Post;
}