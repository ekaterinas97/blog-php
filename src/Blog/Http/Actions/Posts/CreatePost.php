<?php

namespace Geekbrains\Leveltwo\Blog\Http\Actions\Posts;

use Geekbrains\Leveltwo\Blog\Exceptions\AuthException;
use Geekbrains\Leveltwo\Blog\Exceptions\HttpException;
use Geekbrains\Leveltwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Leveltwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Leveltwo\Blog\Http\Actions\ActionInterface;
use Geekbrains\Leveltwo\Blog\Http\Auth\IdentificationInterface;
use Geekbrains\Leveltwo\Blog\Http\ErrorResponse;
use Geekbrains\Leveltwo\Blog\Http\Request;
use Geekbrains\Leveltwo\Blog\Http\Response;
use Geekbrains\Leveltwo\Blog\Http\SuccessfulResponse;
use Geekbrains\Leveltwo\Blog\Post;
use Geekbrains\Leveltwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\Leveltwo\Blog\UUID;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{
    public function __construct(
        private IdentificationInterface $identification,
        private  PostsRepositoryInterface $postsRepository,
        private LoggerInterface $logger,

    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $user = $this->identification->user($request);
        }catch (AuthException $e){
            return new ErrorResponse($e->getMessage());
        }


        // Генерируем UUID для новой статьи
        $newPostUuid = UUID::random();

        try {
            // Пытаемся создать объект статьи
            // из данных запроса
            $post = new Post(
                $newPostUuid,
                $user,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        // Сохраняем новую статью в репозитории
        $this->postsRepository->save($post);
        $this->logger->info("Post created: $newPostUuid");

        // Возвращаем успешный ответ,
        // содержащий UUID новой статьи
        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);

    }
}