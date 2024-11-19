<?php
namespace App\Models;

use CodeIgniter\Model;

class PaymentsModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ["user_id", "booking_id", "amount", "status", "transaction_id", "payment_proof"];

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

    /**
     * Get total revenue for the current month
     *
     * @return float
     */
    public function totalRevenue()
    {
        // Get the current month and year
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Sum all payments for the current month
        $totalRevenue = $this->selectSum('amount')
            ->where('MONTH(created_at)', $currentMonth)  // Filter by current month
            ->where('YEAR(created_at)', $currentYear)   // Filter by current year
            ->where('status', 'Approved')  // Ensure only approved payments are counted
            ->first();

        // If there's no revenue, set it to 0
        return $totalRevenue ? $totalRevenue['amount'] : 0;
    }
}
