<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Schedules;

class ScheduleController extends BaseController
{
    public function index()
    {
        $page = $page ?? 1;  // Default to page 1 if not set

        if ($this->request->getMethod() === 'POST') {

        } else {
            // $scheduleModel = new Schedules();

            // $perPage = 20;  // Define the number of routes to show per page
            // $data['schedules'] =  $data['schedules'] = $scheduleModel
            //         ->select('scheduledRoutes.*, vehicles.tag AS vehicle_tag, vehicles.id AS vehicle_id, routes.id as route_id, routes.name as route_name')
            //         ->join('vehicles', 'scheduledRoutes.vehicle_id = vehicles.id')
            //         ->join('routes', 'scheduledRoutes.route_id = routes.id')
            //         ->paginate($perPage, 'default', $page);
    
            // $data['pager'] = $scheduleModel->pager;  // Get pager object
            // $data['currentPage'] = $page; 
            // $data['totalRoutes'] = $scheduleModel->countAll();  // Get total routes count
            // $data['perPage'] = $perPage;  // Pass per page value to the view
        }
        // return view ('admin/schedules/list', $data);
        return view ('admin/schedules/list');
        
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'vehicle_id' => 'required|max_length[50]|integer',
                'route_id' => 'required|max_length[50]|integer',
            ];
    
            // Define custom error messages
            $errors = [
                'vehicle_id' => [
                    'required' => 'Vehicle ID is required.',
                    'integer' => 'Vehicle ID must be an integer.',
                ],
                'route_id' => [
                    'required' => 'Route ID is required.',
                    'integer' => 'Route ID must be an integer.',
                ],
            ];
    
            // Initial Validation 
            if (!$this->validate($rules, $errors)) {
                // Store the error messages in flash data
                session()->setFlashdata('errors', $this->validator->getErrors());
                return view('admin/schedules/create'); // Return to the view
            }
    
            //Validate count of eta and stops
            if (!isset($_POST['eta']) || !isset($_POST['stop_id']) || count($_POST['eta']) !== count($_POST['stop_id'])) {
                session()->setFlashdata('error', "The number of stops and ETA entries does not match.");
                return view('admin/schedules/create'); // Return to the view
            }

            $this->insert();
        }
    
        // Return the view for the GET request
        return view('admin/schedules/create');
    } 

    public function insert() {
        // Models
        $scheduleModel = new Schedules();

        // Ensure ETA values are valid
        $eta = $this->request->getPost('eta');
        if (empty($eta) || count($eta) < 2) {
            return redirect()->back()->with('errors', 'Invalid ETA data.');
        }
    
        // Check if the vehicle already has a schedule with overlapping times    
        $firstETA = date('Y-m-d H:i:s', strtotime($eta[0]));
        $lastETA = date('Y-m-d H:i:s', strtotime(end($eta)));
        $vehicleId = (int)$this->request->getPost('vehicle_id');
        $existingSchedule = $scheduleModel
            ->where('vehicle_id', $vehicleId)
            ->groupStart()
                ->where('eta <=', $lastETA)
                ->where('eta >=', $firstETA)
            ->groupEnd()
            ->findAll();

        // Handle case where there is an overlapping schedule
        if (!empty($existingSchedule)) {
            return redirect()->back()->with('errors', 'The vehicle has an overlapping schedule.');
        }
    
        //Insert
        $data = [];
        $index = 0;
        $tripId = $this->generateUniqueId();
        $eta = $this->request->getPost('eta');
        $stops = $this->request->getPost('stop_id');

        // Check if the ID already exists in the table
        while ($this->idExists($tripId,$scheduleModel)) {
            // If it exists, generate a new ID
            $tripId = $this->generateUniqueId();
        }

        while($index < count($_POST['eta'])){

        }

    
        // Insert the new schedule
        if ( $scheduleModel->insertBatch($data)) {
          
        } else {
            return redirect()->back()->with('errors', 'Failed to create schedule.');
        }
        
        return redirect()->to('/dashboard/schedules')->with('success', 'Schedule created successfully.');
    }

    private function idExists($id, $model)
    {
        // Access the table name from the passed model
        $tableName = $model->table; // Assuming the model has a `table` property

        // Check if the ID exists in the specified model's table
        $query = $this->db->get_where($tableName, ['id' => $id]);
        return $query->num_rows() > 0;
    }

    function generateUniqueID($length = 50) {
        // Generate random bytes and convert to hexadecimal
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }
}    