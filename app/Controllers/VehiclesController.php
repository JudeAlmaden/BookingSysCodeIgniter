<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Vehicles;
use App\Models\PaymentsModel;
use App\Models\Bookings;

class VehiclesController extends BaseController
{
    public function index($page = null) {
        $page = $page ?? 1;  // Default to page 1 if not set
    
        if ($this->request->getMethod() === 'POST') {
            return $this->addVehicle();
        } else {
            $vehiclesModel = new Vehicles();
            $search = $this->request->getGet('search') ?? '';
            
            if (!empty($search)) {

                $perPage = 20;  // Define the number of routes to show per page
                $data['vehicles'] = $vehiclesModel->like('tag',$search)->paginate($perPage, 'default', $page);
                $data['resultCount'] = $vehiclesModel->like('tag',$search)->countAll();
                
            } else {
                $perPage = 20;  // Define the number of routes to show per page
                $data['vehicles'] = $vehiclesModel->paginate($perPage, 'default', $page);            
                $data['resultCount'] = $vehiclesModel->countAll();  // Get total routes count
            }
            $data['pager'] = $vehiclesModel->pager;  // Get pager object
            $data['currentPage'] = $page; 
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
            'per_kilometer' => $this->request->getPost('per_kilometer'),
            'status'=>"enabled"
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
                return redirect()->back()->with('success', 'Vehicle added successfully.');
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
        try {
            $vehiclesModel = new Vehicles();
    
            if ($tag) {
                $vehicles = $vehiclesModel->like('tag', $tag)->where('status',"enabled")->limit(7)->findAll(); 
            } else {
                $vehicles = $vehiclesModel->findAll(); 
            }
        
            // Prepare the response data
            $response = [];
            
            foreach ($vehicles as $vehicle) {
                $response[] = [
                    'id' => $vehicle['id'],
                    'tag' => $vehicle['tag'],
                ];
            }
        
            // Return the response as JSON
            return $this->response->setJSON($response);
    
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function updateVehicle()
    {
        $vehiclesModel = new Vehicles();
    
        // Prepare data to update
        $updateData = [
            'tag' => $this->request->getPost('tag'),
            'type' => $this->request->getPost('type'),
            'description' => $this->request->getPost('description'),
            'number_seats' => $this->request->getPost('number_seats'),
            'base_fare' => $this->request->getPost('base_fare'),
            'per_kilometer' => $this->request->getPost('per_kilometer'),
        ];
    
        // Validate data
        if ($this->validate([
            'tag' => 'required|min_length[3]|max_length[255]',
            'type' => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[3]|max_length[255]',
            'number_seats' => 'required|integer',
            'base_fare' => 'required|decimal',
            'per_kilometer' => 'required|decimal'
        ])) {
            // Update record in the database
            if ($vehiclesModel->update($this->request->getPost('id'), $updateData)) {
                // Redirect or return success message
                return redirect()->back()->with('success', 'Vehicle updated successfully.');
            } else {
                // Redirect or return error message
                return redirect()->back()->with('error', 'Failed to update vehicle.');
            }
        } else {
            // Redirect or return validation errors
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
    }
    
    public function getVehiclesType($type = null)
    {   
        $vehiclesModel = new Vehicles();
    
        if ($type != "Any") {
            $vehicles = $vehiclesModel->like('type', $type)
            ->groupBy('type')
            ->where('status',"enabled")
            ->limit(7)
            ->findAll(); 
        } else {
            $vehicles = $vehiclesModel->findAll(); 
        }
    
        $response = [];
        foreach ($vehicles as $vehicle) {
            $response[] = [
                'type' => $vehicle['type'],
            ];
        }

        return $this->response->setJSON($response);
    }

    public function toggleVehicle($id)
    {
        $vehiclesModel = new Vehicles(); 
        $bookingsModel = new Bookings();

        $vehicle = $vehiclesModel->find($id);
    
        if ($vehicle) {

            $newStatus = ($vehicle['status'] === 'enabled') ? 'disabled' : 'enabled';
            $vehiclesModel->update($id, ['status' => $newStatus]); 
            $bookingsModel->cancelTripsByVehicle($id);

            session()->setFlashdata('message', 'Vehicle status has been toggled successfully.');
        } else {
            session()->setFlashdata('error', 'Vehicle not found.');
        }
    

        return redirect()->back();
    }
    
}
    