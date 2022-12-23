<?php

namespace Geekbrains\Leveltwo\Blog\Container;

use Geekbrains\Leveltwo\Blog\Exceptions\NotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;

class DIContainer implements ContainerInterface
{
    private array $resolvers = [];

    // Метод для добавления правил
    public function bind(string $type, $resolver)
    {
        $this->resolvers[$type] = $resolver;
    }

    public function get(string $type): object
    {
        if(array_key_exists($type, $this->resolvers)){
            $typeToCreate = $this->resolvers[$type];
            if(is_object($typeToCreate)){
                return $typeToCreate;
            }

            return $this->get($typeToCreate);
        }

        if(!class_exists($type)){
            throw new NotFoundException("Cannot resolve type: $type");
        }

        $reflectionClass = new ReflectionClass($type);

        $constructor = $reflectionClass->getConstructor();

        if($constructor === null){
            return new $type();
        }

        $parameters = [];

        foreach ($constructor->getParameters() as $parameter){
            $parameterType = $parameter->getType()->getName();

            $parameters[] = $this->get($parameterType);
        }

        return new $type(...$parameters);
    }
    public function has(string $type): bool
    {
        // Здесь мы просто пытаемся создать
        // объект требуемого типа
        try {
            $this->get($type);
        } catch (NotFoundException $e) {
            // Возвращаем false, если объект не создан...
            return false;
        }
        // и true, если создан
        return true;
    }
}