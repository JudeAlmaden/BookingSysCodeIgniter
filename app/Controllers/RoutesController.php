<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Routes;
use App\Models\RouteStops;

class RoutesController extends BaseController
{
    public function index($page = null) {
        $page = $page ?? 1;  
    
        if ($this->request->getMethod() === 'POST') {
            return $this->createRoute(); 
        } else {
            $routesModel = new Routes();
            $search = $this->request->getGet('search') ?? '';
    
            $perPage = 20;
    
            if (!empty($search)) {
                $data['routes'] = $routesModel->like('name', $search)  
                                              ->paginate($perPage, 'default', $page);
                $data['resultCount'] = $routesModel->like('name', $search)->countAll(); 
            } else {

                $data['routes'] = $routesModel->paginate($perPage, 'default', $page);
                $data['resultCount'] = $routesModel->countAll();  
            }
    
            $data['pager'] = $routesModel->pager;  
            $data['currentPage'] = $page; 
            $data['perPage'] = $perPage;  
            $data['search'] = $search;  
        }
    
        return view('admin/routes', $data);
    }
    
    
    
    public function viewRoute($id){
        $data = [];
        $routesModel = new Routes();
        $routesStops = new RouteStops();

        $data['route'] = $routesModel->find($id);
        $data['stops'] = $routesStops->where('route_id', $id)->orderBy('index', 'ASC')->findAll();
        $data['totalDistance'] = $routesStops->where('route_id', $id)->selectSum('distance')->first()['distance'] ?? 0;
        $data['totalStops'] = $routesStops->where('route_id', $id)->countAllResults();

        return view('admin/viewRoute', $data);
    }


    public function deleteRoute($id)
    {
        if ($this->request->getMethod() == 'POST') {
            $routesModel = new Routes();
            $routeStopsModel = new RouteStops();
            $routeStopsModel->where('route_id', $id)->delete();
            $routesModel->delete($id);
    
            session()->setFlashdata('success', 'Route and its stops have been deleted successfully.');
        }
    
        // Redirect to the routes page
        return redirect()->to('dashboard/routes/1');
    }

    
    public function createRoute(){

        if ($this->request->getMethod() == 'POST') {
            $rules = [
                'routeName' => 'required|max_length[50]|string',
                'initial' => 'required|max_length[50]|string',
            ];
    
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

            if (!$this->validate($rules, $errors)) {
                session()->setFlashdata('errors', $this->validator);
                return redirect()->back();
            }

            //Validate arrays
            $stations = $_POST['stations'];
            $distances = $_POST['distance'];
            $customErrors = [];

            // Check if stations and distances are arrays with the proper count
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
                return redirect()->to('dashboard/routes/1');
            }
            

            $routesModel = new Routes();
            $exists = $routesModel->where('name', $this->request->getVar('routeName'))->findAll();

            if (!empty($exists)) {
                session()->setFlashdata('errors', 'Name already exsist');
                return redirect()->to('dashboard/routes/1');
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

            //First element has an index and distance of 0 ALWAYS
            $initial = [
                'route_id' => $routeID,
                'name' => $_POST['initial'], 
                'distance' => 0,  
                'index'=> 0,
            ];
            
            //Add frst stop to the array
            array_unshift($routeStopsData, $initial);
            
            foreach ($stations as $index => $station) {
                $routeStopsData[] = [
                    'route_id' => $routeID,
                    'name' => $station,
                    'distance' => $distances[$index],
                    'index'=> $stationIndex++,
                ];
            }
            $routeStopsModel->insertBatch($routeStopsData);
            session()->setFlashdata('success', 'Route Has been created successfully!.');
            return redirect()->to('dashboard/routes/1');
        }
        session()->setFlashdata('errors', 'Incorrect Method');
        return redirect()->to('dashboard/routes/1');
    }

    //Search routes by name
    public function getRoutes($name = null)
    {   
        $routesModel = new Routes();
    
        if ($name) {
            $routes = $routesModel->like('name', $name)
                ->groupBy('name') 
                ->limit(5)      
                ->findAll();
        } else {
            $routes = $routesModel->findAll(); 
        }
        
        $response = [];
        foreach ($routes as $route) {
            $response[] = [
                'id' => $route['id'], 
                'name' => $route['name'],
            ];
        }
    
        return $this->response->setJSON($response);
    }

    //gets stops of a route
    public function getStops($id = null)
    {   
        $routeStopsModel = new RouteStops();
    
        if ($id) {
            $stops = $routeStopsModel->like('route_id', $id)->orderBy('index', 'ASC')->findAll(); 
        } else {
            $stops = $routeStopsModel->findAll();
        }
    
        $response = [];
        foreach ($stops as $stop) {
            $response[] = [
                'id' => $stop['id'], 
                'name' => $stop['name'],
                'distance' => $stop['distance'],
            ];
        }
        return $this->response->setJSON($response);
    }

    //Searches a stp by name
    public function searchStop($str = null){

        $routeStopsModel = new RouteStops();
    
        if ($str) {
            $stops = $routeStopsModel->like('name', $str)->orderBy('index', 'ASC')->groupBy('name')->findAll(); 
        } else {
            $stops = $routeStopsModel->findAll(); 
        }

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