<?php

namespace Geekbrains\Leveltwo\Blog\Repositories\UsersRepository;
use Geekbrains\Leveltwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Leveltwo\Blog\Exceptions\UserAlreadyExistsException;
use Geekbrains\Leveltwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Leveltwo\Blog\User;
use Geekbrains\Leveltwo\Blog\UUID;
use Geekbrains\Leveltwo\Person\Name;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    )
    {
    }
    public function save(User $user): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid, username, first_name, last_name) 
                    VALUES (:uuid, :username, :first_name, :last_name)'
        );
        $statement->execute([
            ':uuid' => (string)$user->uuid(),
            ':username' => $user->username(),
            ':first_name' => $user->name()->first(),
            ':last_name' => $user->name()->last()
        ]);

        $this->logger->info("User created: {$user->uuid()}");
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );
        $statement->execute([':uuid' => (string)$uuid]);

        return $this->getUser($statement, $uuid);
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username '
        );
        $statement->execute([':username' => $username]);
        return $this->getUser($statement, $username);
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    private function getUser(PDOStatement $statement, string $username): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if($result === false){
            $message = "Cannot get user: $username";
            $this->logger->warning($message);
            throw new UserNotFoundException($message);
        }

        return new User(
            new UUID($result['uuid']),
            $result['username'],
            new Name($result['first_name'], $result['last_name'])
        );
    }

    /**
     * @throws UserAlreadyExistsException
     */
    public function checkUserAlreadyExists(string $username): void
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );

        $statement->execute([':username' => $username]);

        $isExists = $statement->fetch(PDO::FETCH_ASSOC);

        if($isExists){
            throw new UserAlreadyExistsException(
                "Username already exists"
            );
        }
    }
}