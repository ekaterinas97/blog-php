<?php

use Geekbrains\Leveltwo\Blog\Commands\Arguments;
use Geekbrains\Leveltwo\Blog\Commands\CreateUserCommand;
use Geekbrains\Leveltwo\Blog\Exceptions\AppException;

use Geekbrains\Leveltwo\Blog\Likes\PostLike;
use Geekbrains\Leveltwo\Blog\Repositories\LikesRepository\CommentsLikesRepositoryInterface;
use Geekbrains\Leveltwo\Blog\Repositories\LikesRepository\PostsLikesRepositoryInterface;
use Geekbrains\Leveltwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\Leveltwo\Blog\UUID;
use Psr\Log\LoggerInterface;


// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';

$usersRepository = $container->get(UsersRepositoryInterface::class);

$postsRepository = $container->get(PostsRepositoryInterface::class);

$postsLikesRepository = $container->get(PostsLikesRepositoryInterface::class);
$user = $usersRepository->getByUsername('newUser');
$post = $postsRepository->get(new UUID('93eb612e-96ab-4df9-85a0-1f216e825d69'));
$like = new PostLike(UUID::random(), $user, $post);

$commentsLikesRepository = $container->get(CommentsLikesRepositoryInterface::class);
$logger = $container->get(LoggerInterface::class);
try {
    $command = $container->get(CreateUserCommand::class);
    $command->handle(Arguments::fromArgv($argv));

} catch (AppException $e){
    $logger->error($e->getMessage(), ['exception'=> $e]);
    echo $e->getMessage();
}