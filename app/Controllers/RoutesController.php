<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Routes;
use App\Models\RouteStops;

class RoutesController extends BaseController
{

    public function index($page = null) {
        $page = $page ?? 1;  // Default to page 1 if not set
    
        if ($this->request->getMethod() === 'POST') {
            return $this->createRoute();  // Handle route creation
        } else {
            $routesModel = new Routes();
    
            $perPage = 20;  // Define the number of routes to show per page
            $data['routes'] = $routesModel->paginate($perPage, 'default', $page);
            $data['pager'] = $routesModel->pager;  // Get pager object
            $data['currentPage'] = $page; 
            $data['totalRoutes'] = $routesModel->countAll();  // Get total routes count
            $data['perPage'] = $perPage;  // Pass per page value to the view
        }
    
        return view('admin/routes', $data);
    }
    
    
    public function viewRoute($id){
        $data = [];
        $routesModel = new Routes();
        $routesStops = new RouteStops();

        $data['route'] = $routesModel->find($id);
        $data['stops'] = $routesStops->where('route', $id)->orderBy('index', 'ASC')->findAll();
        $data['totalDistance'] = $routesStops->where('route', $id)->selectSum('distance')->first()['distance'] ?? 0;
        $data['totalStops'] = $routesStops->where('route', $id)->countAllResults();

        return view('admin/viewRoute', $data);
    }


    public function deleteRoute($id)
    {
        // Check if the request method is POST
        if ($this->request->getMethod() == 'POST') {
            // Load the models
            $routesModel = new Routes();
            $routeStopsModel = new RouteStops();
    
            // First, delete all stops associated with the route
            $routeStopsModel->where('route', $id)->delete();
    
            // Then, delete the route itself
            $routesModel->delete($id);
    
            // Optionally set a success message
            session()->setFlashdata('success', 'Route and its stops have been deleted successfully.');
        }
    
        // Redirect to the routes page
        return redirect()->to('dashboard/routes');
    }

    
    public function createRoute(){

        $data = [];

        if ($this->request->getMethod() == 'POST') {
            // Define validation rules
            $rules = [
                'routeName' => 'required|max_length[50]|string',
                'initial' => 'required|max_length[50]|string',
            ];
    
            // Define custom error messages
            $errors = [
                'routeName' => [
                    'required' => 'Route name is required.',
                    'string' => 'Route name must be a string.',
                ],
                'initial' => [
                    'required' => 'Initial Location is required.',
                    'string' => 'Initial location must be a string.',
                ],
            ];
            //Initial Validation 
            if (!$this->validate($rules, $errors)) {
                session()->setFlashdata('errors', $this->validator);
                return redirect()->to('dashboard/routes');
            }

            //Validate arrays
            $stations = $_POST['stations'];
            $distances = $_POST['distance'];

            // Initialize an array to store custom validation errors
            $customErrors = [];

            // Check if stations and distances are arrays
            if (!is_array($stations) || !is_array($distances)) {
                $customErrors[] = 'Stations and distances must be arrays.';
            } elseif (count($stations) !== count($distances)) {
                $customErrors[] = 'Stations and distances arrays must have the same length.';
            } else {
                // Validate each station and distance
                foreach ($stations as $index => $station) {
                    if (!is_string($station) || strlen($station) < 1 || strlen($station) > 50) {
                        $customErrors[] = "Station at index $index must be a string between 1 and 50 characters.";
                    }
                }
                foreach ($distances as $index => $distance) {
                    if (!is_numeric($distance) || $distance < 0) {
                        $customErrors[] = "Distance at index $index must be a positive integer.";
                    }
                }
            }
            // If there are custom validation errors, return them
            if (!empty($customErrors)) {
                session()->setFlashdata('errors', 'Stations and distances arrays must have the same length.');
                return redirect()->to('dashboard/routes');
            }
            
    
            $routesModel = new Routes();
            $exists = $routesModel->where('name', $this->request->getVar('routeName'))->findAll();

            if (!empty($exists)) {
                session()->setFlashdata('errors', 'Name already exsist');
                return redirect()->to('dashboard/routes');
            }
            
            // Insert the Route
            $newRoute = [
                'name' => $this->request->getVar('routeName'),
            ];
            $routesModel->save($newRoute);
            $routeID = $routesModel->insertID();


            // Insert related route stops
            $routeStopsModel = new RouteStops();
            $routeStopsData = [];
            $stationIndex = 1; 
            $initial = [
                'route' => $routeID,
                'name' => $_POST['initial'], 
                'distance' => 0,  
                'index'=> 0,
            ];
            
            array_unshift($routeStopsData, $initial);
            
            foreach ($stations as $index => $station) {
                $routeStopsData[] = [
                    'route' => $routeID,
                    'name' => $station,
                    'distance' => $distances[$index],
                    'index'=> $stationIndex++,
                ];
            }
            $routeStopsModel->insertBatch($routeStopsData);
            session()->setFlashdata('success', 'Route Has been created successfully!.');
            return redirect()->to('dashboard/routes');
        }
        session()->setFlashdata('errors', 'Incorrect Method');
        return redirect()->to('dashboard/routes');
    }
    public function getRoutes($name = null)
    {   
        $routesModel = new Routes();
    
        // If a name parameter is provided, filter the routes and limit results
        if ($name) {
            $routes = $routesModel->like('name', $name)->limit(5)->findAll(); // Select up to 5 routes with names containing the search term
        } else {
            $routes = $routesModel->findAll(); // Fetch all routes if no name parameter is provided
        }
    
        // Prepare the response data
        $response = [];
        foreach ($routes as $route) {
            $response[] = [
                'id' => $route['id'], // Adjust according to your database schema
                'name' => $route['name'],
            ];
        }
    
        // Return the response as JSON
        return $this->response->setJSON($response);
    }

    public function getStops($id = null)
    {   
        $routeStopsModel = new RouteStops();
    
        // If a name parameter is provided, filter the routes and limit results
        if ($id) {
            $stops = $routeStopsModel->like('route', $id)->orderBy('index', 'ASC')->findAll(); // Select up to 5 routes with names containing the search term
        } else {
            $stops = $routeStopsModel->findAll(); // Fetch all routes if no name parameter is provided
        }
    
        // Prepare the response data
        $response = [];
        foreach ($stops as $stop) {
            $response[] = [
                'id' => $stop['id'], 
                'name' => $stop['name'],
                'distance' => $stop['distance'],
            ];
        }
    
        // Return the response as JSON
        return $this->response->setJSON($response);
    }

    public function searchStop($str = null){

        $routeStopsModel = new RouteStops();
    
        // If a name parameter is provided, filter the routes and limit results
        if ($str) {
            $stops = $routeStopsModel->like('name', $str)->orderBy('index', 'ASC')->findAll(); // Select up to 5 routes with names containing the search term
        } else {
            $stops = $routeStopsModel->findAll(); // Fetch all routes if no name parameter is provided
        }

        // Prepare the response data
        $response = [];
        foreach ($stops as $stop) {
            $response[] = [
                'id' => $stop['id'], 
                'name' => $stop['name'],
            ];
        }
        return $this->response->setJSON($response);

    }
}
