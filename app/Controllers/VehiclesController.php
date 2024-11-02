<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Vehicles;

class VehiclesController extends BaseController
{
    public function index($page = null) {
        $page = $page ?? 1;  // Default to page 1 if not set
    
        if ($this->request->getMethod() === 'POST') {
            return $this->addVehicle();
        } else {
            $vehiclesModel = new Vehicles();
    
            $perPage = 20;  // Define the number of routes to show per page
            $data['vehicles'] = $vehiclesModel->paginate($perPage, 'default', $page);
            $data['pager'] = $vehiclesModel->pager;  // Get pager object
            $data['currentPage'] = $page; 
            $data['totalRoutes'] = $vehiclesModel->countAll();  // Get total routes count
            $data['perPage'] = $perPage;  // Pass per page value to the view
        }
    
        return view('admin/vehicles', $data);
    }

    public function addVehicle(){
        $vehiclesModel = new Vehicles();

        $insert = [
            'tag' => $this->request->getPost('tag'),
            'type' => $this->request->getPost('type'),
            'description' => $this->request->getPost('description'),
            'number_seats' => $this->request->getPost('number_seats'),
            'base_fare' => $this->request->getPost('base_fare'),
            'per_kilometer' => $this->request->getPost('per_kilometer')
        ];

        // Validate data (optional)
        if ($this->validate([
            'tag' => 'required|min_length[3]|max_length[255]',
            'type' => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[3]|max_length[255]',
            'number_seats' => 'required|integer',
            'base_fare' => 'required|decimal',
            'per_kilometer' => 'required|decimal'
        ])) {
            if ($vehiclesModel->insert($insert)) {
                // Redirect or return success message
                return redirect()->to('dashboard/vehicles')->with('success', 'Vehicle added successfully.');
            } else {
                // Redirect or return error message
                return redirect()->back()->with('error', 'Failed to add vehicle.');
            }
        } else {
            // Redirect or return validation errors
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
    }

    public function getVehicles($tag = null)
    {   
        $vehiclesModel = new Vehicles();
    
        if ($tag) {
            $vehicles = $vehiclesModel->like('tag', $tag)->limit(5)->findAll(); 
        } else {
            $vehicles = $vehiclesModel->findAll(); 
        }
    
        // Prepare the response data
        $response = [];
        foreach ($vehicles as $vehicle) {
            $response[] = [
                'id' => $vehicle['id'], // Adjust according to your database schema
                'tag' => $vehicle['tag'],
            ];
        }
    
        // Return the response as JSON
        return $this->response->setJSON($response);
    }
}
    
