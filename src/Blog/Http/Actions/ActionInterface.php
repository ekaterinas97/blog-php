<?php

namespace Geekbrains\Leveltwo\Blog\Http\Actions;

use Geekbrains\Leveltwo\Blog\Http\Request;
use Geekbrains\Leveltwo\Blog\Http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}