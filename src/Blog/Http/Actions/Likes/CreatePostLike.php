<?php

namespace Geekbrains\Leveltwo\Blog\Http\Actions\Likes;

use Geekbrains\Leveltwo\Blog\Exceptions\HttpException;
use Geekbrains\Leveltwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Leveltwo\Blog\Exceptions\LikeAlreadyExists;
use Geekbrains\Leveltwo\Blog\Exceptions\PostNotFoundException;
use Geekbrains\Leveltwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Leveltwo\Blog\Http\Actions\ActionInterface;
use Geekbrains\Leveltwo\Blog\Http\ErrorResponse;
use Geekbrains\Leveltwo\Blog\Http\Request;
use Geekbrains\Leveltwo\Blog\Http\Response;
use Geekbrains\Leveltwo\Blog\Http\SuccessfulResponse;
use Geekbrains\Leveltwo\Blog\Likes\PostLike;
use Geekbrains\Leveltwo\Blog\Repositories\LikesRepository\CommentsLikesRepositoryInterface;
use Geekbrains\Leveltwo\Blog\Repositories\LikesRepository\PostsLikesRepositoryInterface;
use Geekbrains\Leveltwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\Leveltwo\Blog\UUID;

class CreatePostLike implements ActionInterface
{
    public function __construct(
        private PostsLikesRepositoryInterface $likesRepository,
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $postUuid = $request->jsonBodyField('post_uuid');
            $userUuid = $request->jsonBodyField('user_uuid');
        } catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->likesRepository->checkUserLikeForPostExists(new UUID($postUuid), new UUID($userUuid));
        }catch (LikeAlreadyExists | InvalidArgumentException $e){
            return new ErrorResponse($e->getMessage());
        }
        try {
            $newLikeUuid = UUID::random();
            $post = $this->postsRepository->get(new UUID($postUuid));
            $user = $this->usersRepository->get(new UUID($userUuid));
        } catch (InvalidArgumentException | PostNotFoundException | UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $like = new PostLike(
            uuid: $newLikeUuid,
            user: $user,
            post: $post
        );
        $this->likesRepository->save($like);

        return new SuccessfulResponse([
            'uuid' => (string)$newLikeUuid
        ]);
    }
}