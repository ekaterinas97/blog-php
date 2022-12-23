<?php

namespace Geekbrains\Leveltwo\Blog\Http\Actions\Users;

use Geekbrains\Leveltwo\Blog\Exceptions\HttpException;
use Geekbrains\Leveltwo\Blog\Exceptions\UserAlreadyExistsException;
use Geekbrains\Leveltwo\Blog\Http\Actions\ActionInterface;
use Geekbrains\Leveltwo\Blog\Http\ErrorResponse;
use Geekbrains\Leveltwo\Blog\Http\Request;
use Geekbrains\Leveltwo\Blog\Http\Response;
use Geekbrains\Leveltwo\Blog\Http\SuccessfulResponse;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\Leveltwo\Blog\User;
use Geekbrains\Leveltwo\Blog\UUID;
use Geekbrains\Leveltwo\Person\Name;

class CreateUser implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $username = $request->jsonBodyField('username');
        }catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        try{
            $this->usersRepository->checkUserAlreadyExists($username);
        }catch (UserAlreadyExistsException $e){
            return new ErrorResponse($e->getMessage());
        }

        $newUserUuid = UUID::random();

        try {
            $user = new User(
                uuid: $newUserUuid,
                username: $username,
                name: new Name($request->jsonBodyField('first_name'), $request->jsonBodyField('last_name'))
            );
        }catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        $this->usersRepository->save($user);

        return new SuccessfulResponse([
            'uuid' => (string)$newUserUuid
        ]);

    }
}