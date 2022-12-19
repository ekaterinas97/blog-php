<?php

use Geekbrains\Leveltwo\Blog\Exceptions\AppException;
use Geekbrains\Leveltwo\Blog\Exceptions\HttpException;
use Geekbrains\Leveltwo\Blog\Http\Actions\Comments\CreateComment;
use Geekbrains\Leveltwo\Blog\Http\Actions\Posts\CreatePost;
use Geekbrains\Leveltwo\Blog\Http\Actions\Posts\DeletePost;
use Geekbrains\Leveltwo\Blog\Http\Actions\Users\FindByUsername;
use Geekbrains\Leveltwo\Blog\Http\ErrorResponse;
use Geekbrains\Leveltwo\Blog\Http\Request;
use Geekbrains\Leveltwo\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use Geekbrains\Leveltwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;

require_once __DIR__ . '/vendor/autoload.php';

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input')
);

$routes = [
// Добавили ещё один уровень вложенности
// для отделения маршрутов,
// применяемых к запросам с разными методами
    'GET' => [
        '/users/show' => new FindByUsername(
            new SqliteUsersRepository($connection)),
    ],
    'POST' => [
        '/posts/create' => new CreatePost(
            new SqliteUsersRepository($connection),
            new SqlitePostsRepository($connection)
        ),
        '/posts/comment' => new CreateComment(
            new SqliteUsersRepository($connection),
            new SqlitePostsRepository($connection),
            new SqliteCommentsRepository($connection)
        )
    ],
    'DELETE' => [
        '/posts' => new DeletePost(
            new SqlitePostsRepository($connection)
        )
    ]
];

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}
try {
// Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException) {
// Возвращаем неудачный ответ,
// если по какой-то причине
// не можем получить метод
    (new ErrorResponse)->send();
    return;
}
// Если у нас нет маршрутов для метода запроса -
// возвращаем неуспешный ответ
if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Not found'))->send();
    return;
}
// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}
// Выбираем действие по методу и пути
$action = $routes[$method][$path];

try {
    $response = $action->handle($request);
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}
try {
    $response->send();
} catch (JsonException $e) {
}