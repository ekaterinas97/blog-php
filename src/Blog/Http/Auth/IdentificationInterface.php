<?php

namespace Geekbrains\Leveltwo\Blog\Http\Auth;

use Geekbrains\Leveltwo\Blog\Http\Request;
use Geekbrains\Leveltwo\Blog\User;

interface IdentificationInterface
{
    public function user(Request $request): User;
}