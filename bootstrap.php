<?php



use Geekbrains\Leveltwo\Blog\Container\DIContainer;
use Geekbrains\Leveltwo\Blog\Http\Auth\IdentificationInterface;
use Geekbrains\Leveltwo\Blog\Http\Auth\JsonBodyUsernameIdentification;
use Geekbrains\Leveltwo\Blog\Http\Auth\JsonBodyUuidIdentification;
use Geekbrains\Leveltwo\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Geekbrains\Leveltwo\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use Geekbrains\Leveltwo\Blog\Repositories\LikesRepository\CommentsLikesRepositoryInterface;
use Geekbrains\Leveltwo\Blog\Repositories\LikesRepository\PostsLikesRepositoryInterface;
use Geekbrains\Leveltwo\Blog\Repositories\LikesRepository\SqliteCommentsLikesRepository;
use Geekbrains\Leveltwo\Blog\Repositories\LikesRepository\SqlitePostsLikesRepository;
use Geekbrains\Leveltwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\Leveltwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Dotenv\Dotenv;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';

//Загружаем переменные окружения из файла .env
Dotenv::createImmutable(__DIR__)->safeLoad();

// Создаём объект контейнера ..
$container = new DIContainer();
// .. и настраиваем его:

// 1. подключение к БД
$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/' . $_ENV['SQLITE_DB_PATH'])
);

$logger = (new Logger('blog'));

if('yes' === $_ENV['LOG_TO_FILES']){
    $logger->pushHandler(new StreamHandler(
        __DIR__ . '/logs/blog.log'
    ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,
            bubble: false
        ));
}
if('yes' === $_ENV['LOG_TO_CONSOLE']){
    $logger->pushHandler(
        new StreamHandler("php://stdout")
    );
}

$container->bind(
    IdentificationInterface::class,
    JsonBodyUsernameIdentification::class
);
// 2. репозиторий статей
$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);
// 3. репозиторий пользователей
$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepository::class
);

$container->bind(
    CommentsLikesRepositoryInterface::class,
    SqlitePostsLikesRepository::class
);

$container->bind(
    PostsLikesRepositoryInterface::class,
    SqlitePostsLikesRepository::class
);

$container->bind(
    CommentsLikesRepositoryInterface::class,
    SqliteCommentsLikesRepository::class

);



$container->bind(
    LoggerInterface::class,
    $logger
);

// Возвращаем объект контейнера
return $container;