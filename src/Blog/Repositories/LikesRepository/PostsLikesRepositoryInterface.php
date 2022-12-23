<?php

namespace Geekbrains\Leveltwo\Blog\Repositories\LikesRepository;


use Geekbrains\Leveltwo\Blog\Likes\PostLike;
use Geekbrains\Leveltwo\Blog\UUID;

interface PostsLikesRepositoryInterface
{
    public function save(PostLike $like): void;

    public function getByPostUuid(UUID $uuid): array;

    public function checkUserLikeForPostExists(UUID $postUuid, UUID $userUuid): void;

}