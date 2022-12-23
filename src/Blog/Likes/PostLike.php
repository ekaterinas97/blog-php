<?php

namespace Geekbrains\Leveltwo\Blog\Likes;

use Geekbrains\Leveltwo\Blog\Post;
use Geekbrains\Leveltwo\Blog\User;
use Geekbrains\Leveltwo\Blog\UUID;

class PostLike extends Like
{
    private Post $post;

    public function __construct(UUID $uuid, User $user, Post $post)
    {
        parent::__construct($uuid, $user);
        $this->post = $post;
    }

    /**
     * @return Post
     */
    public function post(): Post
    {
        return $this->post;
    }
}