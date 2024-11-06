<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

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
                // Validation passed, proceed with your logic
                return view('customer/homepage');
            }
        } else {
            return view('customer/homepage');
        }        
    }
}
