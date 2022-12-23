<?php

namespace Geekbrains\Leveltwo\Blog\Http\Auth;

use Geekbrains\Leveltwo\Blog\Exceptions\AuthException;
use Geekbrains\Leveltwo\Blog\Exceptions\HttpException;
use Geekbrains\Leveltwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Leveltwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Leveltwo\Blog\Http\Request;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\Leveltwo\Blog\User;
use Geekbrains\Leveltwo\Blog\UUID;

class JsonBodyUuidIdentification implements IdentificationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        try {
            // Получаем UUID пользователя из JSON-тела запроса;
            // ожидаем, что корректный UUID находится в поле user_uuid
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        } catch (HttpException|InvalidArgumentException $e) {
            // Если невозможно получить UUID из запроса -
            // бросаем исключение
            throw new AuthException($e->getMessage());
        }
        try {
            // Ищем пользователя в репозитории и возвращаем его
            return $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
            // Если пользователь с таким UUID не найден -
            // бросаем исключение
            throw new AuthException($e->getMessage());
        }
    }
}