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
                    AND v.type LIKE ?
                    GROUP BY t1.trip_id
                    ORDER BY t1.trip_id, t1.stop_index
                )
                SELECT * FROM trips 
                WHERE capacity > occupied_seats
                  AND departure LIKE ? 
                ORDER BY departure
            ', [$fromLocation, $toLocation, $toLocation, $fromLocation, $toLocation, $fromLocation, $toLocation, $type, $date]);
    
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
                    MAX(t2.ETA) AS arrival
                FROM schedules t1
                INNER JOIN vehicles ON vehicles.id = t1.vehicle_id
                LEFT JOIN schedules t2 ON t2.trip_id = t1.trip_id
                GROUP BY t1.trip_id
            ');

            // Fetch results as an array of objects or arrays
            return $query->getResultArray();
        } catch (Exception $e) {
            // Handle any exceptions
            log_message('error', $e->getMessage());
            return null;
        }
    }
}
