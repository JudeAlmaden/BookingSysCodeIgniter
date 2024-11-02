<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduledRoutes extends Model
{
    protected $table = 'scheduledRoutes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['route_id', 'start_time', 'end_time', 'vehicle_id', 'status', 'created_at', 'updated_at'];

    // Validation rules (optional)
    protected $validationRules = [
        'route_id'    => 'required|integer',
        'start_time'  => 'required|valid_date',
        'end_time'    => 'required|valid_date',
        'vehicle_id'  => 'required|integer',
        'status'      => 'required|string|max_length[50]',
    ];

    // Validation messages (optional)
    protected $validationMessages = [];
    protected $skipValidation = false;

    protected $useTimestamps = true; // Automatically handles created_at and updated_at
    protected $createdField  = 'created_at'; // Name of the field for created timestamp
    protected $updatedField  = 'updated_at'; // Name of the field for updated timestamp
}
