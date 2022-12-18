<?php

use Geekbrains\Leveltwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Leveltwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Leveltwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;

require_once __DIR__ . '/vendor/autoload.php';


$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$usersRepository = new SqliteUsersRepository($connection);
$postsRepository = new SqlitePostsRepository($connection);
//$postsRepository->save(new Post(
//    UUID::random(),
//    new User(UUID::random(), 'user2', new Name('first', 'last')),
//    'Post 1',
//    'some text about something'
//));
try {
    echo $usersRepository->getByUsername('user123') . PHP_EOL;
}catch (InvalidArgumentException | UserNotFoundException  $e) {
    echo $e->getMessage();
}