<?php

namespace Geekbrains\Leveltwo\Blog\Http\Actions\Comments;

use Geekbrains\Leveltwo\Blog\Exceptions\CommentNotFoundException;
use Geekbrains\Leveltwo\Blog\Exceptions\HttpException;
use Geekbrains\Leveltwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Leveltwo\Blog\Http\Actions\ActionInterface;
use Geekbrains\Leveltwo\Blog\Http\ErrorResponse;
use Geekbrains\Leveltwo\Blog\Http\Request;
use Geekbrains\Leveltwo\Blog\Http\Response;
use Geekbrains\Leveltwo\Blog\Http\SuccessfulResponse;
use Geekbrains\Leveltwo\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Geekbrains\Leveltwo\Blog\UUID;

class DeleteComment implements ActionInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $commentUuid = new UUID($request->query('uuid'));
            $this->commentsRepository->get(new UUID($commentUuid));
        }catch (HttpException | InvalidArgumentException | CommentNotFoundException $e){
            return new ErrorResponse($e->getMessage());
        }

        $this->commentsRepository->delete($commentUuid);

        return new SuccessfulResponse([
            'uuid' => (string)$commentUuid
        ]);
    }
}