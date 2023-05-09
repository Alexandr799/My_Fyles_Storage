<?php

namespace App\Controllers;

use App\Entities\Request;
use App\Entities\Response;
use App\Interfaces\Controller;


class Pages extends Controller
{

    public function index(Request $req)
    {
        Response::html('index');
    }
}
