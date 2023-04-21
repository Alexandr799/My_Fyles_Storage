<?php

namespace App\Controllers;

use App\Custom\Filefacade;
use App\Entities\Response;
use App\Interfaces\Controller;


class File extends Controller
{
    public function fileAll()
    {
    }
    public function file()
    {
        $files = Filefacade::getAllFileRecursiveById(Response::getSession('id'));
        Response::json($files);
    }

    public function addFile()
    {
    }

    public function deleteFile()
    {
    }

    public function renameFile()
    {
    }

    public function addDirectory()
    {
    }

    public function renameDirectory()
    {
    }

    public function infoDirectory()
    {
    }

    public function deleteDirectory()
    {
    }
}
