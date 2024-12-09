<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Database\Migrations\Schedules;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SchedulesModel;
use App\Models\PaymentsModel;
use App\Models\Vehicles;
use Exception;
use PDOException;
use App\Models\Bookings;

class BookingController extends BaseController
{
    public function index()
    {
        $data = [];

        if ($this->request->getMethod() == 'POST') {
            $rules = [
                'fromLocation' => 'required|max_length[50]|string',
                'toLocation' => 'required|max_length[50]|string',
                'numSeats' => 'required|integer',
                'type' => 'required|max_length[50]|string',
            ];
        
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

                $schedulesModel = new SchedulesModel();
                $schedules = $schedulesModel->getScheduledTripsFiltered( $fromLocation, $toLocation, $type, $date, $seats);
                
                $data["schedules"] =  $schedules;
                $data["from"] = $fromLocation;
                $data["to"] = $toLocation;
                $data["seats"] = $seats;
                $data["type"] = ($_POST['type'] == 'Any') ? '' : $_POST['type'];
                $data["date"] = ($_POST['date'] == '') ? '' : $_POST['date'];

                return view('customer/homepage', $data);       
            }
 
        } else {    
            return view('customer/homepage');
        }        
    }

    public function book()
    {
        $data = [];
    
        if ($this->request->getMethod() == 'POST') {
            $rules = [
                'trip_id' => 'required|max_length[50]|string',
                'to' => 'required|max_length[50]|string',
                'from' => 'required|max_length[50]|string',
                'seats' => 'required|integer|min_length[1]', 
            ];
    
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
    
            if (!$this->validate($rules, $errors)) {
                // Validation failed
                session()->setFlashdata('errors', $this->validator->getErrors());
                return redirect()->to(base_url('homepage'));
            } else {
                // Check if the user has 5 pending bookings
                $bookingModel = new Bookings();
                $userId = $_SESSION["id"];
                
                $pendingBookingsCount = $bookingModel
                    ->where('user_id', $userId)
                    ->where('status', 'Pending')
                    ->countAllResults();
    
                if ($pendingBookingsCount >= 5) {
                    session()->setFlashdata('errors', 'You already have 5 pending bookings.');
                    return redirect()->to(base_url('homepage'));
                }
    
                // Validation passed
                $trip_id = $this->request->getPost('trip_id');
                $to = $this->request->getPost('to');
                $from = $this->request->getPost('from');
                $seats = $this->request->getPost('seats');
    
                $schedulesModel = new SchedulesModel();
                $validationQuery = $schedulesModel->checkSeatAvailability($from, $to, $seats, $trip_id);
    
                if ($validationQuery && $validationQuery->seat_availability == 'Available') {
                    // Proceed with booking logic and create a booking request
                    $data = [
                        'user_id'  => $userId,
                        'trip_id'  => $trip_id,
                        'distance' => $validationQuery->total_distance,
                        'num_seats'=> $seats,
                        'price'    => ($validationQuery->base_fare + ($validationQuery->per_kilometer * $validationQuery->total_distance)) * $seats,
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
                    session()->setFlashdata('errors', "Looks like there isn't enough seats available");
                    return redirect()->to(base_url('homepage'));
                }
            }
        } else {
            return redirect()->to(base_url('homepage'));
        }
    }
    
    public function bookingsUser()
    {

        $bookingsModel = new Bookings();
        $paymentsModel = new PaymentsModel();
    
        $userId = session()->get('id'); 
    
        if (!$userId) {
            // Set a flash message for the error
            session()->setFlashdata('error', 'You must be logged in to access this page.');
    
            // Redirect to the login page
            return redirect()->to('/login');
        }
    
        // Fetch bookings for the logged-in user, joining with payments table
        $data['bookings'] = $bookingsModel->select('bookings.*, payments.amount, payments.status as payment_status, payments.transaction_id')
        ->join('payments', 'payments.booking_id = bookings.id', 'left') 
        ->where('bookings.user_id', $userId)
        ->orderBy("bookings.status = 'Approved' DESC")  
        ->orderBy('bookings.created_at', 'DESC')  
        ->findAll();

        return view('customer/bookings', $data);
    }

    //Index Page to view bookings
    public function bookingsAdmin($page = null)
    {
        $page = $page ?? 1;  // Default to page 1 if not set
        $bookingsModel = new Bookings();
        $schedules = new SchedulesModel();
        $perPage = 20;
    
        $data['bookings'] = $bookingsModel->select('bookings.*, users.name')  
            ->join('users', 'users.id = bookings.user_id')  
            ->where('bookings.status', 'Pending') 
            ->paginate($perPage, 'default', $page);
    
        foreach ($data['bookings'] as &$booking) {
            // Get current capacity using the trip_id from the booking
            $capacityData = $schedules->getCurrentCapacity($booking['from'], $booking['to'], $booking['trip_id']);
    
            if ($capacityData) {
                // Available seats: Total seats - Reserved seats
                $booking['current_capacity'] = $capacityData->number_seats - $capacityData->reservations;
                $booking['total_distance'] = $capacityData->total_distance;  
                $booking['vehicle_capacity'] = $capacityData->number_seats; 
            } else {
                $booking['current_capacity'] = 'N/A'; 
                $booking['total_distance'] = 'N/A';    
                $booking['vehicle_capacity'] = 'N/A'; 
            }
        }
    
        $data['pager'] = $bookingsModel->pager;
        $data['currentPage'] = $page;
        $data['resultCount'] = $bookingsModel->where('status', 'Pending')->countAllResults();  // Count pending bookings
        $data['perPage'] = $perPage;  

        return view('admin/bookings', $data);
    }
    
    public function approve($bookingId)
    {
        $bookingModel = new Bookings();
        $schedulesModel = new SchedulesModel();

        try {
            $booking = $bookingModel->getBooking($bookingId);            // Get the vehicle seat cap

            if ($booking) {
                // Get the current reservation count for the trip
                $seatCount = $schedulesModel->getCurrentCapacity($booking->from,$booking->to, $booking->trip_id );

                // Calculate total reservations (existing + new booking)
                $totalReservations = $seatCount->reservations + $bookingModel->find($bookingId)['num_seats'];
    
                // Check if total reservations exceed vehicle seat capacity
                if ($totalReservations <= $seatCount->number_seats) {
                    $bookingModel->update($bookingId, ['status' => 'Approved']);
                    $schedulesModel->approveReservation($bookingId, $bookingModel->find($bookingId)['num_seats']);
    
                    return redirect()->to(base_url('dashboard/bookings/1'))->with('success', 'Booking approved successfully');
                } else {

                    session()->setFlashdata('errors', 'Not enough seats available on the vehicle');
                    return redirect()->to(base_url('dashboard/bookings/1'));
                }
            } else {

                session()->setFlashdata('errors', 'Could not find the vehicle or trip details');
                return redirect()->to(base_url('dashboard/bookings/1'));
            }
        } catch (\Exception $e) {
            // Handle any potential errors gracefully
            session()->setFlashdata('errors', 'An error occurred while approving the booking: ' . $e->getMessage());
            return redirect()->to(base_url('dashboard/bookings/1'));
        }
    }
    
    public function decline($bookingId)
    {
        $bookingModel = new Bookings();
        
        // Update the booking status to 'Declined'
        $bookingModel->update($bookingId, ['status' => 'Declined']);
        
        // Redirect with success message
        return redirect()->to(base_url('dashboard/bookings/1'))->with('success', 'Booking declined successfully');
    }

    public function cancelBooking($bookingId)
    {
        try {
            $bookingModel = new Bookings();
            // Attempt to cancel the booking and update the payment status
            $success = $bookingModel->cancelBookingAndPayment($bookingId);
    
            if ($success) {
                return redirect()->to('dashboard/schedules/1')->with('success', 'Booking and payment status updated successfully.');
            } else {
                return redirect()->to('dashboard/schedules/1')->with('error', 'Failed to update booking or payment status.');
            }
        } catch (\Exception $e) {
            // In case of an error
            return redirect()->to('/dashboard/vehicles')->with('error', $e->getMessage());
        }
    }
    
}