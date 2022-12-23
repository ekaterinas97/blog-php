<?php

namespace Geekbrains\Leveltwo\Blog\Likes;

use Geekbrains\Leveltwo\Blog\Comment;
use Geekbrains\Leveltwo\Blog\User;
use Geekbrains\Leveltwo\Blog\UUID;

class CommentLike extends Like
{
    private Comment $comment;

    public function __construct(UUID $uuid, User $user, Comment $comment)
    {
        parent::__construct($uuid, $user);
        $this->comment=$comment;
    }

    /**
     * @return Comment
     */
    public function comment(): Comment
    {
        return $this->comment;
    }
}