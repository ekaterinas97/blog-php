<?php

use Geekbrains\Leveltwo\Blog\Commands\Arguments;
use Geekbrains\Leveltwo\Blog\Commands\CreateUserCommand;
use Geekbrains\Leveltwo\Blog\Exceptions\AppException;
use Geekbrains\Leveltwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Leveltwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Leveltwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;

require_once __DIR__ . '/vendor/autoload.php';


$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$usersRepository = new SqliteUsersRepository($connection);
$postsRepository = new SqlitePostsRepository($connection);

$command = new CreateUserCommand($usersRepository);
//$postsRepository->save(new Post(
//    UUID::random(),
//    new User(UUID::random(), 'user2', new Name('first', 'last')),
//    'Post 1',
//    'some text about something'
//));
try {
    echo $usersRepository->getByUsername('user123') . PHP_EOL;
//    $command->handle(Arguments::fromArgv($argv));
}catch (AppException  $e) {
    echo $e->getMessage();
}

//print_r($argv);
//
//$arr = [];
//
//foreach ($argv as $item){
//   $parts = explode('=', $item);
//   if(count($parts) !== 2){
//       continue;
//   }
//   $arr[$parts[0]] = $parts[1];
//}
//print_r($arr);

$json = '{"foo-bar": 12345}';

$obj = json_decode($json, true);
print_r($obj);
