<?php

namespace App\Interfaces;
use App\Entities\Request;


class Controller
{
    protected $nextMiddleware=null;
    public static function create()
    {
        return new static();
    }

    public function next(Controller $mid): Controller
    {
        $this->nextMiddleware = $mid;
        return $this;
    }

    protected function  nextController(Request $req, string $method)
    {
        if (!empty($this->nextMiddleware)) {
            return $this->nextMiddleware->handle($req, $method);
        }
        return $this->$method($req);
    }

    // при создании наследника тут может быть прописан свая функция для мидвара 
    // и в случае если требуется вызвать следующий нужно вызвать метод nextController и передать ему запрос и метод
    public function handle(Request $req, string $method)
    {
        $this->nextController($req, $method);
    }
}
