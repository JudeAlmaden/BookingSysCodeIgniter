<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduledStops extends Model
{
    protected $table = 'scheduledStops';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['scheduled_route_id', 'stop_id', 'ETA'];

    // Validation rules (optional)
    protected $validationRules = [
        'scheduled_route_id' => 'required|integer',
        'stop_id'            => 'required|integer',
        'ETA'                => 'required|valid_date',
    ];

    // Validation messages (optional)
    protected $validationMessages = [];
    protected $skipValidation = false;

    protected $useTimestamps = true; // Automatically handles created_at and updated_at
    protected $createdField  = 'created_at'; // Name of the field for created timestamp
    protected $updatedField  = 'updated_at'; // Name of the field for updated timestamp
}
