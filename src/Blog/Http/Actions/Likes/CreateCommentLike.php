<?php

namespace Geekbrains\Leveltwo\Blog\Http\Actions\Likes;

use Geekbrains\Leveltwo\Blog\Exceptions\CommentNotFoundException;
use Geekbrains\Leveltwo\Blog\Exceptions\HttpException;
use Geekbrains\Leveltwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Leveltwo\Blog\Exceptions\LikeAlreadyExists;
use Geekbrains\Leveltwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Leveltwo\Blog\Http\Actions\ActionInterface;
use Geekbrains\Leveltwo\Blog\Http\ErrorResponse;
use Geekbrains\Leveltwo\Blog\Http\Request;
use Geekbrains\Leveltwo\Blog\Http\Response;
use Geekbrains\Leveltwo\Blog\Http\SuccessfulResponse;
use Geekbrains\Leveltwo\Blog\Likes\CommentLike;
use Geekbrains\Leveltwo\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Geekbrains\Leveltwo\Blog\Repositories\LikesRepository\CommentsLikesRepositoryInterface;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\Leveltwo\Blog\UUID;

class CreateCommentLike implements ActionInterface
{
    public function __construct(
        private CommentsLikesRepositoryInterface $commentsLikesRepository,
        private CommentsRepositoryInterface $commentsRepository,
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $commentUuid = $request->jsonBodyField('comment_uuid');
            $userUuid = $request->jsonBodyField('user_uuid');
        }catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->commentsLikesRepository->checkUserLikeForCommentExists(new UUID($commentUuid), new UUID($userUuid));
        }catch (LikeAlreadyExists | InvalidArgumentException $e){
            return new ErrorResponse($e->getMessage());
        }

        try{
            $newCommentLikeUUid = UUID::random();
            $comment = $this->commentsRepository->get(new UUID($commentUuid));
            $user = $this->usersRepository->get(new UUID($userUuid));
        }catch (CommentNotFoundException | UserNotFoundException | InvalidArgumentException $e){
            return new ErrorResponse($e->getMessage());
        }

        $commentLike = new CommentLike(
            uuid: $newCommentLikeUUid,
            user: $user,
            comment: $comment
        );

        $this->commentsLikesRepository->save($commentLike);

        return new SuccessfulResponse([
            'uuid' => (string)$newCommentLikeUUid
        ]);
    }
}