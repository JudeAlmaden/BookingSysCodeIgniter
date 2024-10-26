<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class VehiclesController extends BaseController
{
    public function index($page = null) {
        $page = $page ?? 1;  // Default to page 1 if not set
    
        if ($this->request->getMethod() === 'POST') {

        } else {
    
            $routesPerPage = 20;  // Define the number of routes to show per page

            $data['currentPage'] =             $routesPerPage = 20;  // Define the number of routes to show per page
            $data['totalRoutes'] =             $routesPerPage = 20;  // Define the number of routes to show per page
            $data['routesPerPage'] =             $routesPerPage = 20;  // Define the number of routes to show per page
        }
    
        return view('admin/busses', $data);
    }
}
    
