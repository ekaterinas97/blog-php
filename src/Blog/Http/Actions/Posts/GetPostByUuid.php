<?php

namespace Geekbrains\Leveltwo\Blog\Http\Actions\Posts;

use Geekbrains\Leveltwo\Blog\Exceptions\HttpException;
use Geekbrains\Leveltwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Leveltwo\Blog\Exceptions\PostNotFoundException;
use Geekbrains\Leveltwo\Blog\Http\Actions\ActionInterface;
use Geekbrains\Leveltwo\Blog\Http\ErrorResponse;
use Geekbrains\Leveltwo\Blog\Http\Request;
use Geekbrains\Leveltwo\Blog\Http\Response;
use Geekbrains\Leveltwo\Blog\Http\SuccessfulResponse;
use Geekbrains\Leveltwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\Leveltwo\Blog\UUID;

class GetPostByUuid implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $postUuid = new UUID($request->query('uuid'));
            $post = $this->postsRepository->get($postUuid);
        }catch (HttpException | InvalidArgumentException | PostNotFoundException $e){
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'title' => $post->title(),
            'text' => $post->text(),
            'author' => $post->author()->username()
        ]);
    }
}