<?php

namespace App\Entities;

class Request
{
    private array $parameters;
    private array $arguments;
    private array $path;

    private array $files;

    public array $props = [];

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

    public function getParamAll()
    {
        return $this->parameters;
    }

    public function getArg(string $argName)
    {
        return $this->arguments[$argName];
    }

    public function getArgAll()
    {
        return $this->arguments;
    }
}
