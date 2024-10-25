<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AdminController extends BaseController
{
    public function index(){
        return view('dashboard');
    }

    public function routes(){
        return view('admin/routes');
    }
}
