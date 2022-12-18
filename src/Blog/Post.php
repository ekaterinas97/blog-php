<?php
namespace Geekbrains\Leveltwo\Blog;

class Post
{
    public function __construct(
        private UUID $uuid,
        private User $author,
        private string $title,
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
     * @return User
     */
    public function author(): User
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
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
        return "Post: $this->title written by $this->author";
    }
}