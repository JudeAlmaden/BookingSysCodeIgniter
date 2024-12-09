<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;
use PDOException;

class Bookings extends Model
{
    protected $table            = 'bookings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id','trip_id', 'distance','num_seats', 'price', 'status','from','to'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    
    //
    public function updateCancelledTrip($id)
    {
        try {
            // Connect to the database
            $db = \Config\Database::connect();
            
            // Prepare the SQL query to perform a LEFT JOIN with the payments table
            $sql = "UPDATE bookings
                    JOIN payments ON payments.booking_id = bookings.id
                    SET bookings.status = 'Cancelled', payments.status = 'Waiting for refund'
                    WHERE bookings.trip_id = ? AND payments.status = 'Approved'";

            // Execute the query with the bound parameter for trip_id
            $query = $db->query($sql, [$id]);
            
            // Check if the update was successful
            if ($db->affectedRows() > 0) {
                return redirect()->back()->with('success', 'All bookings for the trip have been marked as waiting for refund.');
            } else {
                return redirect()->back()->with('error', 'No bookings found for the specified trip or update failed.');
            }
        } catch (\Exception $e) {
            // Handle any errors
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
     
    public function countAcceptedBookingsForCurrentMonth()
    {
        try {
            // Get the current month and year
            $currentMonth = date('m');
            $currentYear = date('Y');

            // Query the database to count the bookings with status 'Accepted' for the current month
            $builder = $this->builder();
            $builder->select('COUNT(*) as accepted_bookings_count')
                    ->where('status', 'Confirmed')  // Filter by "Accepted" status
                    ->where('MONTH(created_at)', $currentMonth)  // Filter by current month
                    ->where('YEAR(created_at)', $currentYear);  // Filter by current year

            // Execute the query and fetch the result
            $query = $builder->get();
            $result = $query->getRowArray();

            // Return the count
            return $result ? $result['accepted_bookings_count'] : 0;
        } catch (Exception $e) {
            // Handle any errors
            return 0;
        }
    }

    public function getBooking($bookingId)
    {
        // Update the reservations for the schedule
        $db = \Config\Database::connect();
        $getBookingQuery = "
            SELECT * FROM bookings WHERE id = ?
        ";
        $query = $db->query($getBookingQuery, [$bookingId]);
        
        // Return the first row of the result
        return $query->getRow();
    }
    
    public function cancelTripsByVehicle($vehicleId)
    {
        $db = \Config\Database::connect();

        $scheduleModel = new SchedulesModel();
        $bookingsModel = new Bookings();

        try {
            // Get all distinct trip_ids for the given vehicle
            $query = "
                SELECT DISTINCT schedules.trip_id 
                FROM vehicles
                INNER JOIN schedules ON schedules.vehicle_id = vehicles.id
                WHERE vehicles.id = ?
            ";

            // Execute the query to get trip ids
            $tripIds = $db->query($query, [$vehicleId])->getResultArray();

            // If no trip_ids found
            if (empty($tripIds)) {
                return redirect()->back()->with('error', 'No trips found for the specified vehicle.');
            }

            // Start a database transaction to ensure all updates are done atomically
            $db->transBegin();

            // Loop through the trip_ids and perform the necessary updates
            foreach ($tripIds as $trip) {
                $tripId = $trip['trip_id'];
                
                $updateData = [
                    'status' => 'Cancelled',  // Assuming 'status' column exists in the schedules table
                ];
                
                // Set the satus of the trip to cancelled
                $scheduleModel->where('trip_id', $tripId)->set($updateData)->update();
                $bookingsModel->updateCancelledTrip($trip);//Set the bookings related to the trip to waiting for refund, cancelled
            }

            if ($db->transStatus() === false) {
                // Rollback if something went wrong
                $db->transRollback();
                return redirect()->back()->with('error', 'An error occurred while canceling the trips.');
            }

            $db->transCommit();

            return redirect()->back()->with('success', 'All bookings for the trips have been marked as cancelled and waiting for refund.');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function cancelBookingAndPayment($bookingId)
    {
        $db = \Config\Database::connect();
        $scheduleModel = new SchedulesModel();
    
        try {
            // Start a transaction
            $db->transBegin();
    
            // Update the booking status to 'Cancelled' and the payment status to 'Waiting for refund'
            $db->query("
                UPDATE bookings
                SET status = 'Cancelled'
                WHERE id = ?
            ", [$bookingId]);
    
            $db->query("
                UPDATE payments
                SET status = 'Waiting for refund'
                WHERE booking_id = ? AND status = 'Approved'
            ", [$bookingId]);
    
            // Update the reservations count in the schedules table
            $scheduleModel->cancelReservation($bookingId);
    
            // Commit the transaction if everything was successful
            $db->transCommit();
            return true;
        } catch (\Exception $e) {
            // Rollback in case of error
            $db->transRollback();
            throw new \Exception('An error occurred: ' . $e->getMessage());
        }
    }
    
}
