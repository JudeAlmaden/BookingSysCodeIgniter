<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SchedulesModel;

class ScheduleController extends BaseController
{
    public function index($page = null)
    {
        $page = $page ?? 1; 
        $scheduleModel = new SchedulesModel();
        $perPage = 20;  

        // Get the result as an array
        $data["schedules"] = $scheduleModel->getTripsWithDepartureArrival();
        $data['pager'] = $scheduleModel->pager;  // Get pager object
        $data['currentPage'] = $page; 
        $data['totalRoutes'] = $scheduleModel->countAll();  // Get total routes count
        $data['perPage'] = $perPage;  // Pass per page value to the view

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
    
            //Validate count of eta, stops, and name of stop 
            //Ensure that the number of stops is not less than 2
            if (!isset($_POST['eta']) || !isset($_POST['name']) || !isset($_POST['distance'])) {
                session()->setFlashdata('error', "The number of data entry for stops does not match.");
                return view('admin/schedules/create'); // Return to the view

            }

            $this->insert();
        }
    
        // Return the view for the GET request
        return view('admin/schedules/create');
    } 

    public function insert() {
        // Models
        $scheduleModel = new SchedulesModel();

        // get the arrays
        $eta = $this->request->getPost('eta');
        $stopName = $this->request->getPost('name');
        $distance = $this->request->getPost('distance');
        $vehicleId = (int)$this->request->getPost('vehicle_id');

        // Check if the vehicle already has a schedule with overlapping times    
        $firstETA = date('Y-m-d H:i:s', strtotime($eta[0]));
        $lastETA = date('Y-m-d H:i:s', strtotime(end($eta)));
        
        // Handle case where there is an overlapping schedule, if there is then exit 
        if ($this->alreadyHasSchedule($firstETA, $lastETA, $vehicleId)) {
            return redirect()->back()->with('errors', 'The vehicle has an overlapping schedule.');

        }else{
            //Insert
            $data = [];
            $index = 0;
            $tripId = $this->generateUniqueId();
            $eta = $this->request->getPost('eta');

            // create a unique Id for the scheduled tip
            while ($this->idExists($tripId)) {
                // If it exists, generate a new ID
                $tripId = $this->generateUniqueId();
            }

            //Add each scheduled stop
            while($index < count($_POST['eta'])){
                $data[] = [
                    'vehicle_id' => $vehicleId,
                    'trip_id' => $tripId, // Ensure stop_id is cast to int
                    'stop_name'=> $stopName[$index],
                    'distance'=> $distance[$index],
                    'ETA' => $eta[$index],
                    'stop_index'=> $index,
                    'status'=> "Available",
                ];
                $index ++;
            }
            // Insert the new schedule and return
            if ($scheduleModel->insertBatch($data)) {
                return redirect()->to('/dashboard/schedules')->with('success', 'Schedule created successfully.');

            } else {
                return redirect()->back()->with('errors', 'Failed to create schedule.');
            }
        }   
    }

    private function idExists($id){
        // Instantiate the model directly
        $scheduleModel = new SchedulesModel();
    
        // Use the model’s query builder to check if the ID exists
        return $scheduleModel->where('id', $id)->countAllResults() > 0;
    }
    
    private function alreadyHasSchedule($start, $end, $vehicle_id)
    {
        $scheduleModel = new SchedulesModel();
    
        // Query to check for any overlapping schedules
        $rs = $scheduleModel
            ->where('vehicle_id', $vehicle_id)
            ->groupStart()
                // New schedule starts before existing ends and ends after existing starts
                ->where('eta <', $end) // New schedule starts before existing schedule ends
                ->where('eta >', $start) // New schedule ends after existing schedule starts
            ->groupEnd()
            ->findAll();
    
        return count($rs) > 0; // If there are any results, there is an overlap
    }

    function generateUniqueID() {    
        // Instantiate the model
        $scheduleModel = new SchedulesModel();
    
        // Get the maximum trip_id value from the table
        $maxTripId = $scheduleModel->selectMax('trip_id')->first();
    
        // If there are no records, use 0 as the starting point
        $newTripId = ($maxTripId['trip_id'] ?? 0) + 1;
    
        return $newTripId;
    }
    
    function view($id){
        return view ('admin/schedules/view');
    }
}    