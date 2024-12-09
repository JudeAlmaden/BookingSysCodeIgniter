<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Bookings;
use App\Models\PaymentsModel;
use App\Models\SchedulesModel; // Assuming this model exists

class AdminController extends BaseController
{
    public function index()
    {
        $paymentsM = new PaymentsModel();
        $totalRevenue = $paymentsM->totalRevenue();
    
        $bookingsM = new Bookings();
        $totalAcceptedBookings = $bookingsM->countAcceptedBookingsForCurrentMonth();
    
        $tripsM = new SchedulesModel();
        $totalTripsThisMonth = $tripsM->countTripsForCurrentMonth();
    
    
        // Pass the data to the view
        return view('dashboard', [
            'totalRevenue' => $totalRevenue,
            'totalAcceptedBookings' => $totalAcceptedBookings,
            'totalTripsThisMonth' => $totalTripsThisMonth,
        ]);
    }
    
}