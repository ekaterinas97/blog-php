<?php

use Geekbrains\Leveltwo\Blog\Exceptions\AppException;
use Geekbrains\Leveltwo\Blog\Exceptions\HttpException;
use Geekbrains\Leveltwo\Blog\Http\Actions\Comments\CreateComment;
use Geekbrains\Leveltwo\Blog\Http\Actions\Comments\DeleteComment;
use Geekbrains\Leveltwo\Blog\Http\Actions\Likes\CreateCommentLike;
use Geekbrains\Leveltwo\Blog\Http\Actions\Likes\CreatePostLike;
use Geekbrains\Leveltwo\Blog\Http\Actions\Posts\CreatePost;
use Geekbrains\Leveltwo\Blog\Http\Actions\Posts\GetPostByUuid;
use Geekbrains\Leveltwo\Blog\Http\Actions\Users\CreateUser;
use Geekbrains\Leveltwo\Blog\Http\Actions\Users\FindByUsername;
use Geekbrains\Leveltwo\Blog\Http\ErrorResponse;
use Geekbrains\Leveltwo\Blog\Http\Request;
use Psr\Log\LoggerInterface;


// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input')
);

// Ассоциируем маршруты с именами классов действий,
// вместо готовых объектов
$routes = [
    'GET' => [
        '/users/show' => FindByUsername::class,
        '/posts/show' => GetPostByUuid::class
    ],
    'POST' => [
        '/posts/create' => CreatePost::class,
        '/users/create' => CreateUser::class,
        '/posts/comment' => CreateComment::class,
        '/postLikes/create' => CreatePostLike::class,
        '/commentLikes/create' => CreateCommentLike::class
    ],
    'DELETE' => [
        '/comment' => DeleteComment::class,
    ]
];

try {
    $path = $request->path();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}
try {
    $method = $request->method();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

if (!array_key_exists($method, $routes) || !array_key_exists($path, $routes[$method])) {
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}
// Получаем имя класса действия для маршрута
$actionClassName = $routes[$method][$path];

// С помощью контейнера
// создаём объект нужного действия
$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
} catch (AppException $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse($e->getMessage()))->send();
    return;
}
$response->send();
