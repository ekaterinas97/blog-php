<?php

namespace Geekbrains\Blog\UnitTests\Actions;

use Geekbrains\Blog\UnitTests\DummyLogger;
use Geekbrains\Leveltwo\Blog\Exceptions\PostNotFoundException;
use Geekbrains\Leveltwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Leveltwo\Blog\Http\Actions\Posts\CreatePost;
use Geekbrains\Leveltwo\Blog\Http\Auth\IdentificationInterface;
use Geekbrains\Leveltwo\Blog\Http\Request;
use Geekbrains\Leveltwo\Blog\Http\SuccessfulResponse;
use Geekbrains\Leveltwo\Blog\Post;
use Geekbrains\Leveltwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\Leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\Leveltwo\Blog\User;
use Geekbrains\Leveltwo\Blog\UUID;
use Geekbrains\Leveltwo\Person\Name;
use Monolog\Test\TestCase;

class CreatePostActionTest extends TestCase
{
    private function postsRepository() : PostsRepositoryInterface
    {
        return new class() implements PostsRepositoryInterface {
            private bool $called = false;

            public function __construct()
            {
            }
            public function save(Post $post): void
            {
                $this->called = true;
            }
            public function get(UUID $uuid): Post
            {
                throw new PostNotFoundException("Not found");
            }
            public function delete(UUID $uuid): void
            {
            }
            public function getCalled(): bool
            {
                return $this->called;
            }
        };
    }
    private function usersRepository(array $users): UsersRepositoryInterface
    {
        return new class($users) implements UsersRepositoryInterface{
            public function __construct(
                private array $users
            )
            {
            }

            public function save(User $user): void
          {
          }
          public function get(UUID $uuid): User
          {
              foreach ($this->users as $user){
                  if($user instanceof User && (string)$uuid === (string)$user->uuid()){
                      return $user;
                  }
              }
              throw new UserNotFoundException("Cannot get user: $uuid");
          }
          public function getByUsername(string $username): User
          {
              throw new UserNotFoundException("Not found");
          }
          public function checkUserAlreadyExists(string $username): void
          {
              // TODO: Implement checkUserAlreadyExists() method.
          }
        };
    }
    private function Identification(): IdentificationInterface
    {
        return new class() implements IdentificationInterface{
            public function user(Request $request): User
            {
                return new User(
                    new UUID('133180cc-dfa7-4618-a2bf-8d8e648e0ed2'),
                    'newUser',
                    new Name('first', 'last')
                );
            }
        };
    }
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request([], [], '{"username":"newUser","title":"title","text":"text"}');

        $postsRepository = $this->postsRepository();

        $usersRepository = $this->usersRepository([
            new User(
                new UUID('10373537-0805-4d7a-830e-22b481b4859c'),
                'newUser',
                new Name('first', 'last')
            )
        ]);

        $identification = $this->Identification();

        $action = new CreatePost($identification, $postsRepository, new DummyLogger());
        $user = $identification->user($request);


        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);

        $this->setOutputCallback(function ($data){

            $dataDecode = json_decode(
                $data,
                associative: true,
                flags: JSON_THROW_ON_ERROR
            );

            $dataDecode['data']['uuid'] = "75a00d81-d6e3-4812-8ef0-a1cc2829c934";

            return json_encode(
                $dataDecode,
                JSON_THROW_ON_ERROR
            );
        });

        $this->expectOutputString(
            '{"success": true,"data": {"uuid": "75a00d81-d6e3-4812-8ef0-a1cc2829c934"}}'
        );

        $response->send();

    }

}