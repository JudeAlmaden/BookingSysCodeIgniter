<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\Routes;
use App\Models\RouteStops;
use CodeIgniter\HTTP\ResponseInterface;

class AdminController extends BaseController
{
    public function index(){
        return view('dashboard');
    }

}

