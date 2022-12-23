<?php

namespace Geekbrains\Leveltwo\Blog\Http\Auth;

use Geekbrains\Leveltwo\Blog\Exceptions\AuthException;
use Geekbrains\Leveltwo\Blog\Exceptions\HttpException;
use Geekbrains\Leveltwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Leveltwo\Blog\Http\Request;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\Leveltwo\Blog\User;

class JsonBodyUsernameIdentification implements IdentificationInterface
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
        try{
            $username = $request->jsonBodyField('username');
        }catch (HttpException $e){
            throw new AuthException($e->getMessage());
        }

        try {
            return $this->usersRepository->getByUsername($username);
        }catch (UserNotFoundException $e){
            throw new AuthException($e->getMessage());
        }
    }
}