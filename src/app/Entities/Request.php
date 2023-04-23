<?php

namespace App\Entities;

class Request
{
    private array $parameters;
    private array $arguments;
    private array $path;

    private array $files;

    private array $props = [];

    public function __construct(array $params, array $args)
    {
        $this->parameters = $params;
        $this->arguments = $args;
        $this->path = $_SERVER;
        $this->files = $_FILES;
    }

    public function getParam(string $paramName)
    {
        return $this->parameters[$paramName];
    }

    public function getArg(string $argName)
    {
        return $this->arguments[$argName];
    }

    public function setInProps(string $key, mixed $value): void
    {
        $this->props[$key] = $value;
    }

    public function getProps(string $key): mixed
    {
        return $this->props[$key];
    }

    public function getFile(string $key){
        return $this->files[$key];
    }
}