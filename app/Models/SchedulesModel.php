<?php

namespace App\Models;
use Exception;
use PDOException;

use CodeIgniter\Model;

class SchedulesModel extends Model
{
    protected $table = 'schedules';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    // Define allowed fields
    protected $allowedFields = [
        'vehicle_id',
        'trip_id',
        'ETA',
        'stop_index',
        'status',
        'stop_name',
        'distance',
        'reservations',
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Define validation rules
    protected $validationRules = [
        'vehicle_id'   => 'required|integer',
        'trip_id'      => 'required|integer',
        'ETA'          => 'required|valid_date',
        'stop_index'   => 'required|integer',
        'status'       => 'required|max_length[50]',
        'stop_name'     => 'required|max_length[50]',
        'distance'     => 'required|integer'
    ];

    // Custom error messages (optional)
    protected $validationMessages = [
        'vehicle_id' => [
            'required' => 'Vehicle ID is required.',
            'integer'  => 'Vehicle ID must be an integer.'
        ],
        'trip_id' => [
            'required'   => 'Trip ID is required.',
            'max_length' => 'Trip ID cannot exceed 50 characters.'
        ],
        'ETA' => [
            'required'   => 'ETA is required.',
            'valid_date' => 'ETA must be a valid date.'
        ],
        'stop_index' => [
            'required' => 'Stop index is required.',
            'integer'  => 'Stop index must be an integer.'
        ],
        'status' => [
            'required'   => 'Status is required.',
            'max_length' => 'Status cannot exceed 50 characters.'
        ],
        'stopName' => [
            'required'   => 'Stop name is required.',
            'max_length' => 'Stop name cannot exceed 50 characters.'
        ],
        'distance' => [
            'required' => 'Distance is required.',
            'integer'  => 'Distance must be an integer.'
        ]
    ];

    // Disable automatic validation for mass assignments (optional)
    protected $skipValidation = false;

    
    public function getScheduledTripsFiltered($fromLocation, $toLocation, $type, $date, $seats) {
        try {
            $db = \Config\Database::connect();
            $currentTime = date('Y-m-d H:i:s'); // Get the current time
    
            $query = $db->query('
                WITH trips AS (
                    SELECT 
                        t1.trip_id, 
                        t1.vehicle_id AS vehicleID, 
                        v.tag AS vehicle_name,
                        v.type,
                        v.base_fare,
                        v.per_kilometer,
                        v.number_seats AS capacity,
                        (v.number_seats - MAX(t1.reservations)) AS available_seats,
                        MAX(t1.reservations) AS occupied_seats,
                        t1.reservations,
                        (SELECT SUM(dist.distance) 
                         FROM schedules dist 
                         WHERE dist.trip_id = t1.trip_id 
                           AND dist.stop_index BETWEEN (
                                SELECT stop_index 
                                FROM schedules 
                                WHERE trip_id = t1.trip_id AND stop_name = ?
                           ) + 1 AND (
                                SELECT stop_index 
                                FROM schedules 
                                WHERE trip_id = t1.trip_id AND stop_name = ?
                           )
                        ) AS total_distance,
                        t1.stop_index AS from_stop_index,
                        t1.stop_name AS from_stop_name,
                        t1.ETA AS departure,
                        t2.stop_name AS to_stop_name, 
                        t2.ETA AS arrival,
                        t2.stop_index AS to_stop_index
                    FROM schedules t1
                    JOIN vehicles v ON v.id = t1.vehicle_id
                    JOIN schedules t2 ON t2.trip_id = t1.trip_id AND t2.stop_name = ?
                    WHERE t1.trip_id IN (
                            SELECT trip_id 
                            FROM schedules 
                            WHERE stop_name IN (?, ?)
                    )
                    AND t1.stop_index BETWEEN (
                        SELECT stop_index 
                        FROM schedules 
                        WHERE trip_id = t1.trip_id AND stop_name = ?
                    ) AND (
                        SELECT stop_index 
                        FROM schedules 
                        WHERE trip_id = t1.trip_id AND stop_name = ?
                    ) 
                    AND t1.status = "Available"
                    AND v.type LIKE ?
                    GROUP BY t1.trip_id
                    ORDER BY t1.trip_id, t1.stop_index
                )
                SELECT * FROM trips 
                WHERE 
                    available_seats >= reservations + ?
                    AND departure LIKE ?
                    AND (
                        ? LIKE "%" AND departure > ?
                    )
                ORDER BY departure
            ', [
                $fromLocation, $toLocation, $toLocation, $fromLocation, $toLocation, 
                $fromLocation, $toLocation, $type, $seats, $date, $date, $currentTime
            ]);
    
            return $query->getResultArray();
    
        } catch (Exception $e) {
            session()->setFlashdata('errors', $e->getMessage());
            return null;
        }
    }
    

    public function checkSeatAvailability($from, $to, $seats, $trip_id)
    {
        try {
            $db = \Config\Database::connect();

            $query = $db->query('
                SELECT 
                    t1.trip_id,
                    vehicles.number_seats,
                    vehicles.base_fare,
                    vehicles.per_kilometer,
                    t1.reservations,
                    (SELECT SUM(dist.distance) 
                     FROM schedules dist 
                     WHERE dist.trip_id = t1.trip_id 
                       AND dist.stop_index BETWEEN (
                           SELECT stop_index 
                           FROM schedules 
                           WHERE trip_id = t1.trip_id AND stop_name = ?
                       ) + 1 
                       AND (
                           SELECT stop_index 
                           FROM schedules 
                           WHERE trip_id = t1.trip_id AND stop_name = ?
                       )
                    ) AS total_distance,
                    CASE 
                        WHEN (t1.reservations + ?) <= vehicles.number_seats THEN "Available"
                        ELSE "Not Available"
                    END AS seat_availability
                FROM schedules t1
                INNER JOIN vehicles ON t1.vehicle_id = vehicles.id
                WHERE t1.trip_id = ?
                GROUP BY t1.trip_id;
            ', [$from, $to, $seats, $trip_id]);

            // Fetch the row as an object or array
            return $query->getRow();
        } catch (Exception $e) {
            // Handle any exceptions
            log_message('error', $e->getMessage());
            return null; // or handle error as appropriate
        }
    }

    public function getCurrentCapacity($from, $to, $trip_id)
    {
        try {
            $db = \Config\Database::connect();

            // Query to fetch the current capacity and reservations
            $query = $db->query('
                SELECT 
                    t1.trip_id,
                    vehicles.number_seats,
                    t1.reservations,
                    (SELECT SUM(dist.distance) 
                    FROM schedules dist 
                    WHERE dist.trip_id = t1.trip_id 
                    AND dist.stop_index BETWEEN (
                        SELECT stop_index 
                        FROM schedules 
                        WHERE trip_id = t1.trip_id AND stop_name = ?
                    ) + 1 
                    AND (
                        SELECT stop_index 
                        FROM schedules 
                        WHERE trip_id = t1.trip_id AND stop_name = ?
                    )
                    ) AS total_distance
                FROM schedules t1
                INNER JOIN vehicles ON t1.vehicle_id = vehicles.id
                WHERE t1.trip_id = ?
                GROUP BY t1.trip_id;
            ', [$from, $to, $trip_id]);

            // Fetch the row as an object or array
            return $query->getRow();
        } catch (Exception $e) {
            // Handle any exceptions
            log_message('error', $e->getMessage());
            return null; // or handle error as appropriate
        }
    }

    public function getTripsWithDepartureArrival()
    {
        try {
            $db = \Config\Database::connect();

            // Run the query and get trips with departure and arrival times
            $query = $db->query('
            SELECT 
                t1.trip_id, 
                vehicles.tag AS vehicle_name, 
                MIN(t2.ETA) AS departure, 
                t1.status AS trip_status,
                MAX(t2.ETA) AS arrival
            FROM schedules t1
            INNER JOIN vehicles ON vehicles.id = t1.vehicle_id
            LEFT JOIN schedules t2 ON t2.trip_id = t1.trip_id
            GROUP BY t1.trip_id
            ORDER BY 
                CASE 
                    WHEN t1.status = "Available" THEN 0
                    ELSE 1
                END, 
                departure DESC
        ');
        

            // Fetch results as an array of objects or arrays
            return $query->getResultArray();
        } catch (Exception $e) {
            // Handle any exceptions
            log_message('error', $e->getMessage());
            return null;
        }
    }


    public function cancelledReservation($bookingId)
    {
    $db = \Config\Database::connect();
    $builder = $db->table('schedules');

    $sql = "
    UPDATE schedules t1
    SET t1.reservations = t1.reservations - (
        SELECT bookings.num_seats
        FROM bookings
        WHERE bookings.id = ?
    )
    WHERE t1.trip_id IN (
        SELECT bookings.trip_id
        FROM bookings
        WHERE bookings.id = ?
    )
    AND t1.stop_index BETWEEN (
        SELECT t4.stop_index 
        FROM schedules t4 
        WHERE t4.trip_id = t1.trip_id 
        AND t4.stop_name = (SELECT bookings.from FROM bookings WHERE bookings.id = ?)
    ) AND (
        SELECT t5.stop_index 
        FROM schedules t5 
        WHERE t5.trip_id = t1.trip_id 
        AND t5.stop_name = (SELECT bookings.to FROM bookings WHERE bookings.id = ?)-1
    )
    ";


    // Bind the booking ID to all placeholders
    $result = $db->query($sql, [$bookingId, $bookingId, $bookingId, $bookingId]);

    return $result ? true : false;
    }

    public function approveReservation($bookingId)
    {
    $db = \Config\Database::connect();
    $builder = $db->table('schedules');

    $sql = "
    UPDATE schedules t1
    SET t1.reservations = t1.reservations + (
        SELECT bookings.num_seats
        FROM bookings
        WHERE bookings.id = ?
    )
    WHERE t1.trip_id IN (
        SELECT bookings.trip_id
        FROM bookings
        WHERE bookings.id = ?
    )
    AND t1.stop_index BETWEEN (
        SELECT t4.stop_index 
        FROM schedules t4 
        WHERE t4.trip_id = t1.trip_id 
        AND t4.stop_name = (SELECT bookings.from FROM bookings WHERE bookings.id = ?)
    ) AND (
        SELECT t5.stop_index 
        FROM schedules t5 
        WHERE t5.trip_id = t1.trip_id 
        AND t5.stop_name = (SELECT bookings.to FROM bookings WHERE bookings.id = ?)-1
    )
    ";


    // Bind the booking ID to all placeholders
    $result = $db->query($sql, [$bookingId, $bookingId, $bookingId, $bookingId]);

    return $result ? true : false;
    }


    public function getPassengersFromStop($id)
    {
        try {
            // Connect to the database
            $db = \Config\Database::connect();
    
            // Prepare the query with the bound parameter for schedules.id
            $query = $db->query('
                SELECT 
                    schedules.stop_name AS currentStop,
                    users.name as passenger, 
                    users.phone_no,
                    b1.from, 
                    b1.to,
                    b1.price, 
                    b1.num_seats,
                    IFNULL(payments.amount, 0) AS amount_paid,
                    IFNULL(payments.status, "Not paid") AS payment_status,
                    CASE 
                        WHEN (
                            SELECT t5.stop_index 
                            FROM `schedules` t5 
                            WHERE t5.trip_id = schedules.trip_id 
                              AND t5.stop_name = b1.to
                            LIMIT 1
                        ) = schedules.stop_index THEN "Dropoff"
                        WHEN (
                            SELECT t5.stop_index 
                            FROM `schedules` t5 
                            WHERE t5.trip_id = schedules.trip_id 
                              AND t5.stop_name = b1.from
                            LIMIT 1
                        ) = schedules.stop_index THEN "Boarding"
                        ELSE "Seated"
                    END AS passenger_status
                FROM `schedules`
                INNER JOIN `bookings` b1 ON b1.trip_id = schedules.trip_id
                LEFT JOIN `payments` ON b1.id = payments.booking_id
                LEFT JOIN users ON b1.user_id = users.id
                WHERE schedules.id = ?
                  AND b1.status = "Approved"
                  AND schedules.stop_index BETWEEN (
                      SELECT t4.stop_index 
                      FROM `schedules` t4 
                      WHERE t4.trip_id = schedules.trip_id 
                        AND t4.stop_name = b1.from
                      LIMIT 1
                  ) AND (
                      SELECT t4.stop_index 
                      FROM `schedules` t4 
                      WHERE t4.trip_id = schedules.trip_id 
                        AND t4.stop_name = b1.to
                      LIMIT 1
                  );
            ', [$id]); // Bind $id to the query
    
            // Fetch results as an array of objects or arrays
            return $query->getResultArray();
        } catch (\Exception $e) {
            // Handle any exceptions and log the error message
            log_message('error', 'Error in getPassengersFromStop function: ' . $e->getMessage());
            return null; // Return null in case of error
        }
    }
    
    public function countTripsForCurrentMonth()
    {
        try {
            $db = \Config\Database::connect();
    
            // Get the current date
            $currentDate = date('Y-m-d');
            
            // Query to count completed trips for this month
            $query = $db->query('
                SELECT COUNT(DISTINCT t1.trip_id) AS completed_trips_count
                FROM schedules t1
                INNER JOIN vehicles ON vehicles.id = t1.vehicle_id
                WHERE t1.status = "Completed"
                AND t1.ETA BETWEEN ? AND ?
            ', [$currentDate.' 00:00:00', $currentDate.' 23:59:59']); // Filter by current month
    
            // Fetch the result as an array
            $result = $query->getRowArray();
    
            // Return the completed trips count
            return $result ? (int)$result['completed_trips_count'] : 0;
    
        } catch (Exception $e) {
            log_message('error', 'Error in getCompletedTripsThisMonth: ' . $e->getMessage());
            return 0; // Return 0 in case of error
        }
    }
    
}
