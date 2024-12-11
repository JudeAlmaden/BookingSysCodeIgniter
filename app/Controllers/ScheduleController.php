<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SchedulesModel;
use App\Models\Bookings;

class ScheduleController extends BaseController
{
    public function index($page = null)
    {
        $page = $page ?? 1; 
        $scheduleModel = new SchedulesModel();
        $perPage = 20;  

        $data["schedules"] = $scheduleModel->getTripsWithDepartureArrival();
        $data['pager'] = $scheduleModel->pager; 
        $data['currentPage'] = $page; 
        $data['resultCount'] = $scheduleModel->countAll(); 
        $data['perPage'] = $perPage; 

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
                return view('admin/schedules/create'); 
            }
    
            //Validate count of eta, stops, and name of stop 
            //Ensure that the number of stops is not less than 2
            if (!isset($_POST['eta']) || !isset($_POST['name']) || !isset($_POST['distance'])) {
                session()->setFlashdata('error', "The number of data entry for stops does not match.");
                return view('admin/schedules/create'); 

            }

            $this->insert();
        }
    
        // Return the view for the GET request
        return view('admin/schedules/create');
    } 

    public function insert() {
        $scheduleModel = new SchedulesModel();

        $eta = $this->request->getPost('eta');
        $stopName = $this->request->getPost('name');
        $distance = $this->request->getPost('distance');
        $vehicleId = (int)$this->request->getPost('vehicle_id');

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

            while ($this->idExists($tripId)) {
                // If it exists, generate a new ID
                $tripId = $this->generateUniqueId();
            }


            while($index < count($_POST['eta'])){
                $data[] = [
                    'vehicle_id' => $vehicleId,
                    'trip_id' => $tripId, 
                    'stop_name'=> $stopName[$index],
                    'distance'=> $distance[$index],
                    'ETA' => $eta[$index],
                    'stop_index'=> $index,
                    'status'=> "Available",
                ];
                $index ++;
            }

            if ($scheduleModel->insertBatch($data)) {
                return redirect()->to('/dashboard/schedules')->with('success', 'Schedule created successfully.');

            } else {
                return redirect()->back()->with('errors', 'Failed to create schedule.');
            }
        }   
    }

    private function idExists($id){
        $scheduleModel = new SchedulesModel();
        return $scheduleModel->where('id', $id)->countAllResults() > 0;
    }
    
    private function alreadyHasSchedule($start, $end, $vehicle_id)
    {
        $scheduleModel = new SchedulesModel();
    
        // Query to check for any overlapping schedules
        $rs = $scheduleModel
        ->where('vehicle_id', $vehicle_id)
        ->where('status', 'Available') 
        ->groupStart()
            ->where('eta <', $end) 
            ->where('eta >', $start) 
        ->groupEnd()
        ->findAll();
    
        return count($rs) > 0; 
    }

    //Generatign a uniqu trip id
    function generateUniqueID() {    
        $scheduleModel = new SchedulesModel();
        $maxTripId = $scheduleModel->selectMax('trip_id')->first();
        $newTripId = ($maxTripId['trip_id'] ?? 0) + 1;
        return $newTripId;
    }
    
    //List of scheduled trips
    public function view($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('schedules');
    
        // Query schedules and join with vehicles table
        $builder->select('schedules.reservations,schedules.id, schedules.ETA, schedules.stop_index,
        schedules.status,schedules.stop_name,schedules.distance, vehicles.tag, vehicles.type, vehicles.number_seats')
                ->join('vehicles', 'vehicles.id = schedules.vehicle_id') 
                ->where('schedules.trip_id', $id); 
    
        $schedules = $builder->get()->getResultArray();
    
        $vehicle = [
            'tag' => $schedules[0]['tag'] ?? null,
            'type' => $schedules[0]['type'] ?? null,
            'number_seats' => $schedules[0]['number_seats'] ?? null,
        ];
    
        // Pass both schedules and vehicle data to the view
        return view('admin/schedules/view', ['schedules' => $schedules, 'vehicle' => $vehicle, 'id'=>$id ]);
    }
    
    //Summarized list of pasengers for each stop on a scheduled trip
    public function viewReservations($id)
    {
        $scheduleModel = new SchedulesModel();
        $passengers = $scheduleModel->getPassengersFromStop($id);
        $stop = $scheduleModel->where('id', $id)->first(); 
        
        if ($passengers === null || empty($passengers)) {
            session()->setFlashdata('error', 'No passengers found for this schedule.');
        }
    
        return view('admin/schedules/reservationsView', [
            'passengers' => $passengers,
            'stop' => $stop  
        ]);
    }
    

    //Cancels a trip and sets bookings and refunds
    public function cancelTrip($id)
    {
        // Load the SchedulesModel and Bookings model
        $scheduleModel = new SchedulesModel();
        $bookingsModel = new Bookings();
        
        // Prepare the update data for schedules
        $updateData = [
            'status' => 'Cancelled',  // Assuming 'status' column exists in the schedules table
        ];
        
        // Update the schedules where trip_id matches the given $id
        if ($scheduleModel->where('trip_id', $id)->set($updateData)->update() && $bookingsModel->updateCancelledTrip($id)) {
            // Redirect to the view schedule page with success message
            return redirect()->back()->with('success', 'The trip has been successfully cancelled.');
        } else {
            // Redirect back with error message if the operation fails
            return redirect()->back()->with('error', 'Failed to cancel the trip. Please try again.');
        }
    }

    
    //Mark a scheduled trip as completed
    public function completeTrip($id)
    {
        $scheduleModel = new SchedulesModel();        
        $updateData = [
            'status' => 'Completed',  
        ];
        
        if ($scheduleModel->where('trip_id', $id)->set($updateData)->update()) {
            return redirect()->back()->with('success', 'The trip has been successfully cancelled.');
        } else {
            return redirect()->back()->with('error', 'Failed to cancel the trip. Please try again.');
        }
    }
}    