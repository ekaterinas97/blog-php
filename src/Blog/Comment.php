<?php

namespace Geekbrains\Leveltwo\Blog;

class Comment
{
    public function __construct(
        private UUID $uuid,
        private Post $post,
        private User $author,
        private string $text
    )
    {
    }

    /**
     * @return UUID
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return Post
     */
    public function post(): Post
    {
        return $this->post;
    }

    /**
     * @return User
     */
    public function author(): User
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function text(): string
    {
        return $this->text;
    }

    public function __toString(): string
    {
        return "Comment: $this->text written by $this->author";
    }
}