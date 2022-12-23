<?php

namespace Geekbrains\Leveltwo\Blog\Repositories\LikesRepository;


use Geekbrains\Leveltwo\Blog\Likes\CommentLike;
use Geekbrains\Leveltwo\Blog\UUID;

interface CommentsLikesRepositoryInterface
{
    public function save(CommentLike $like): void;

    public function getByCommentUuid(UUID $uuid): array;

    public function checkUserLikeForCommentExists(UUID $commentUuid, UUID $userUuid): void;
}