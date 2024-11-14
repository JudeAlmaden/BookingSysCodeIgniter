<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SchedulesModel;
use Exception;
use PDOException;
use App\Models\Bookings;

class BookingController extends BaseController
{
    public function index()
    {
        $data = [];

        if ($this->request->getMethod() == 'POST') {
            // Define validation rules
            $rules = [
                'fromLocation' => 'required|max_length[50]|string',
                'toLocation' => 'required|max_length[50]|string',
                'numSeats' => 'required|integer',
                'type' => 'required|max_length[50]|string',
            ];
        
            // Custom error messages
            $errors = [
                'fromLocation' => [
                    'required' => 'Starting location is required.',
                    'string' => 'Route name must be a string.',
                ],
                'toLocation' => [
                    'required' => 'Destination is required.',
                    'string' => 'Initial location must be a string.',
                ],
                'numSeats' => [
                    'required' => 'Number of seats is required.',
                    'integer' => 'Number of seats must be an integer.',
                ],
                'type' => [
                    'required' => 'Type of vehicle is required.',
                    'string' => 'Type must be a string.',
                ],
            ];
        
            // Initial Validation 
            if (!$this->validate($rules, $errors)) {
                // Store validation errors in flashdata
                session()->setFlashdata('errors', $this->validator->getErrors());
                return view('customer/homepage');
            } else {

                $fromLocation = ($_POST['fromLocation'] == '') ? '%' : $_POST['fromLocation'];
                $toLocation = ($_POST['toLocation'] == '') ? '%' : $_POST['toLocation'];
                $type = ($_POST['type'] == 'Any') ? '%' : $_POST['type'];
                $date = ($_POST['date'] == '') ? '%' : $_POST['date'];
                $seats = ($_POST['numSeats'] == '') ? 1 : $_POST['numSeats'];

                //Get the data we want from model
                $schedulesModel = new SchedulesModel();
                $schedules = $schedulesModel->getScheduledTripsFiltered( $fromLocation, $toLocation, $type, $date, $seats);
                
                //Add it for view
                $data["schedules"] =  $schedules;
                $data["from"] = $fromLocation;
                $data["to"] = $toLocation;
                $data["seats"] = $seats;
                $data["type"] = ($_POST['type'] == 'Any') ? '' : $_POST['type'];
                $data["date"] = ($_POST['date'] == '') ? '' : $_POST['date'];

                // Pass the data to the view
                return view('customer/homepage', $data);
                
            }
            
        } else {    
            return view('customer/homepage');
        }        
    }

    public function book(){
        $data = [];
    
        if ($this->request->getMethod() == 'POST') {
            // Define validation rules
            $rules = [
                'trip_id' => 'required|max_length[50]|string',
                'to' => 'required|max_length[50]|string',
                'from' => 'required|max_length[50]|string',
                'seats' => 'required|integer|min_length[1]', // added validation for seats
            ];
    
            // Custom error messages
            $errors = [
                'trip_id' => [
                    'required' => 'Trip ID is required.',
                    'max_length' => 'Trip ID should not exceed 50 characters.',
                    'string' => 'Trip ID must be a string.',
                ],
                'to' => [
                    'required' => 'Destination is required.',
                    'max_length' => 'Destination should not exceed 50 characters.',
                    'string' => 'Destination must be a string.',
                ],
                'from' => [
                    'required' => 'Destination is required.',
                    'max_length' => 'Destination should not exceed 50 characters.',
                    'string' => 'Destination must be a string.',
                ],
                'seats' => [
                    'required' => 'Number of seats is required.',
                    'integer' => 'Number of seats must be an integer.',
                    'min_length' => 'Seats cannot be less than 1.',
                ],
            ];
    
            // Validate data
            if (!$this->validate($rules, $errors)) {
                // Validation failed
                session()->setFlashdata('errors', $this->validator->getErrors());
                return redirect()->to(base_url('homepage')); 

            } else {
                // Validation passed
                $trip_id = $this->request->getPost('trip_id');
                $to = $this->request->getPost('to');
                $from = $this->request->getPost('from');
                $seats = $this->request->getPost('seats');
                
                $schedulesModel = new SchedulesModel();
                $validationQuery =  $schedulesModel->checkSeatAvailability($from, $to, $seats, $trip_id);

                if ($validationQuery && $validationQuery->seat_availability == 'Available') {
                    // Proceed with booking logic and create a booking request
                    $bookingModel = new Bookings();
                    $data = [
                        'user_id'  => $_SESSION["id"],
                        'trip_id'  => $trip_id,
                        'distance' => $validationQuery->total_distance,
                        'num_seats'=> $seats,
                        'price'    => ($validationQuery->base_fare + ($validationQuery->per_kilometer*$validationQuery->total_distance))*$seats,
                        'status'   => 'Pending', 
                        'from'     => $from,
                        'to'       => $to,
                    ];
                    
                    if ($bookingModel->insert($data)) {
                        session()->setFlashdata('success', 'Booking successful!');
                        return redirect()->to(base_url('homepage'));

                    } else {
                        session()->setFlashdata('errors', $bookingModel->errors());
                        return redirect()->to(base_url('homepage/book'));  
                    }
                } else {
                    session()->setFlashdata('errors',"Looks like there isn't enough seats available");
                    return redirect()->to(base_url('homepage'));     
                }

            }
        }
        else{
            return redirect()->to(base_url('homepage')); 
        }
    }
 
}
