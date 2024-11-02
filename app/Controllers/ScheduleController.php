<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ScheduledRoutes;
use App\Models\ScheduledStops;

class ScheduleController extends BaseController
{
    public function index()
    {
        $page = $page ?? 1;  // Default to page 1 if not set

        if ($this->request->getMethod() === 'POST') {

        } else {
            $scheduledRoutes = new ScheduledRoutes();

            $perPage = 20;  // Define the number of routes to show per page
            $data['schedules'] =  $data['schedules'] = $scheduledRoutes
                    ->select('scheduledRoutes.*, vehicles.tag AS vehicle_tag, vehicles.id AS vehicle_id, routes.id as route_id, routes.name as route_name')
                    ->join('vehicles', 'scheduledRoutes.vehicle_id = vehicles.id')
                    ->join('routes', 'scheduledRoutes.route_id = routes.id')
                    ->paginate($perPage, 'default', $page);
    
            $data['pager'] = $scheduledRoutes->pager;  // Get pager object
            $data['currentPage'] = $page; 
            $data['totalRoutes'] = $scheduledRoutes->countAll();  // Get total routes count
            $data['perPage'] = $perPage;  // Pass per page value to the view
        }
        return view ('admin/schedules/list', $data);
        
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
    
            //Validate count
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
        $schedule = new ScheduledRoutes();
        $routeStop = new ScheduledStops();
        $eta = $this->request->getPost('eta');
    
        // Ensure ETA values are valid
        if (empty($eta) || count($eta) < 2) {
            return redirect()->back()->with('errors', 'Invalid ETA data.');
        }
    
        // Check if the vehicle already has a schedule with overlapping times    
        $firstETA = date('Y-m-d H:i:s', strtotime($eta[0]));
        $lastETA = date('Y-m-d H:i:s', strtotime(end($eta)));
    
        $vehicleId = (int)$this->request->getPost('vehicle_id');
        $existingSchedule = $schedule
            ->where('vehicle_id', $vehicleId)
            ->groupStart()
                ->where('start_time <=', $lastETA)
                ->where('end_time >=', $firstETA)
            ->groupEnd()
            ->findAll();
    
        if (!empty($existingSchedule)) {
            // Handle case where there is an overlapping schedule
            return redirect()->back()->with('errors', 'The vehicle has an overlapping schedule.');
        }
    
        // Prepare data for insertion
        $scheduleData = [
            'route_id' => (int)$this->request->getPost('route_id'),
            'start_time' => $firstETA,
            'end_time' => $lastETA,
            'vehicle_id' => $vehicleId,
            'status' => "Available",
        ];
    
        // Insert the new schedule
        if ($schedule->insert($scheduleData)) {
            $insertedID = $schedule->insertID();
    
            // Initialize the array to hold stops data
            $routeStopsData = [];
            $stops = $this->request->getPost('stop_id');
    
            // Format each 'ETA' value and prepare data for insertion
            foreach ($stops as $index => $stop) {
                $formattedETA = date('Y-m-d H:i:s', strtotime($eta[$index]));
    
                $routeStopsData[] = [
                    'scheduled_route_id' => $insertedID,
                    'stop_id' => (int)$stop, // Ensure stop_id is cast to int
                    'ETA' => $formattedETA,
                ];
            }
    
            // Insert stops data into the database
            $routeStop->insertBatch($routeStopsData); // Use insertBatch to insert multiple rows at once
            
        } else {
            return redirect()->back()->with('errors', 'Failed to create schedule.');
        }
        return redirect()->to('/dashboard/schedules')->with('success', 'Schedule created successfully.');
    }
}    