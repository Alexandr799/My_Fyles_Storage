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
        return array_key_exists($paramName, $this->parameters) ? $this->parameters[$paramName] : null;
    }

    public function getArg(string $argName)
    {
        return array_key_exists($argName, $this->arguments) ? $this->arguments[$argName] : null;
    }

    public function setInProps(string $key, mixed $value): void
    {
        $this->props[$key] = $value;
    }

    public function getProps(string $key): mixed
    {
        return array_key_exists($key, $this->props) ? $this->props[$key] : null;
    }

    public function getFile(string $key): array | null
    {
        return array_key_exists($key, $this->files) ? $this->files[$key] : null;
    }
}
