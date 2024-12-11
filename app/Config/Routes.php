<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --- Generic Authentication Routes ---
$routes->match(['get', 'post'], '/register', 'User::register', ['filter' => 'noauth']); // Register route
$routes->match(['get', 'post'], '/login', 'User::login', ['filter' => 'noauth']);           // Login route (redirects to dashboard)
$routes->match(['get', 'post'], '/', 'User::login', ['filter' => 'noauth']);                 // Root route (redirects to login)

// --- Admin Dashboard Routes ---
$routes->get('/dashboard', 'AdminController::index', ['filter' => 'isAdmin']); // Dashboard home
$routes->get('dashboard/bookings/(:num)', 'BookingController::bookingsAdmin/$1', ['filter' => 'isAdmin']); // View bookings (paginated)
$routes->get('dashboard/vehicles/(:num)', 'VehiclesController::index/$1', ['filter' => 'isAdmin']); // Vehicles management (paginated), handles adding of vehicle
$routes->post('dashboard/vehicles/', 'VehiclesController::index', ['filter' => 'isAdmin']); 
$routes->match(['get', 'post'], 'dashboard/routes/(:num)', 'RoutesController::index/$1', ['filter' => 'isAdmin']);
$routes->post( 'dashboard/routes/', 'RoutesController::index', ['filter' => 'isAdmin']); // Routes management (paginated), handles adding of route
$routes->get('dashboard/schedules/(:num)', 'ScheduleController::index/$1', ['filter' => 'isAdmin']); // Paginated schedules
$routes->get('dashboard/payments/(:num)', 'PaymentController::index/$1');
$routes->get('dashboard/refunds/(:num)', 'PaymentController::refund/$1', ['filter' => 'isAdmin']);


// Toggling vehicle
$routes->get('dashboard/vehicles/toggle/(:num)', 'VehiclesController::toggleVehicle/$1', ['filter' => 'isAdmin']); // Get vehicles by type
$routes->post('dashboard/vehicles/update', 'VehiclesController::updateVehicle', ['filter' => 'isAdmin']); // Get vehicles by type

// Route Manipulation
$routes->match(['get', 'post'], 'dashboard/routes/view/(:num)', 'RoutesController::viewRoute/$1', ['filter' => 'isAdmin']); // View route details
$routes->post('dashboard/routes/delete/(:num)', 'RoutesController::deleteRoute/$1', ['filter' => 'isAdmin']);           // Delete route

// Schedule Crud
$routes->match(['get', 'post'], 'dashboard/schedules/create', 'ScheduleController::create', ['filter' => 'isAdmin']); // Create new schedule
$routes->get('dashboard/schedule/view/(:num)', 'ScheduleController::view/$1', ['filter' => 'isAdmin']); //View
$routes->get('dashboard/schedules/reservations/(:num)', 'ScheduleController::viewReservations/$1', ['filter' => 'isAdmin']); //View
$routes->get('dashboard/schedules/cancel/(:num)', 'ScheduleController::cancelTrip/$1', ['filter' => 'isAdmin']); // Get vehicles by type
$routes->get('dashboard/schedules/complete/(:num)', 'ScheduleController::completeTrip/$1', ['filter' => 'isAdmin']); // Get vehicles by type
$routes->get('dashboard/cancel-booking/(:num)', 'BookingController::cancelBooking/$1');


//Payments
$routes->get('dashboard/payment/view/(:num)', 'PaymentController::viewPayment/$1', ['filter' => 'isAdmin']);
$routes->post('payment/approve/(:num)', 'PaymentController::approve/$1', ['filter' => 'isAdmin']);
$routes->post('payment/reject/(:num)', 'PaymentController::reject/$1', ['filter' => 'isAdmin']);

//Booking
$routes->get('dashboard/bookings/approve/(:num)', 'BookingController::approve/$1', ['filter' => 'isAdmin']);
$routes->get('dashboard/bookings/decline/(:num)', 'BookingController::decline/$1', ['filter' => 'isAdmin']);

//refund
$routes->post('refund/complete/(:num)', 'PaymentController::completeRefund/$1', ['filter' => 'isAdmin']);

// --- API Routes ---
$routes->get('routes/get/(:any)', 'RoutesController::getRoutes/$1', ['filter' => 'noauth']); // Fetch route data by ID
$routes->get('vehicles/get/(:any)', 'VehiclesController::getVehicles/$1', ['filter' => 'noauth']); // Fetch vehicle data by ID
$routes->get('stops/get/(:num)', 'RoutesController::getStops/$1', ['filter' => 'noauth']); // Get stops associated with a route
$routes->get('stops/search/(:any)', 'RoutesController::searchStop/$1', ['filter' => 'noauth']); // Search stops by name
$routes->get('vehicles/type/get/(:any)', 'VehiclesController::getVehiclesType/$1', ['filter' => 'noauth']); // Get vehicles by type

// --- User Profile and Logout ---
$routes->get('/profile', 'User::profile', ['filter' => 'auth']); 
$routes->get('/logout', 'User::logout'); 

// --- User Booking Routes ---
$routes->match(['get', 'post'], 'homepage', 'BookingController::index', ['filter' => 'auth']); // Booking homepage (authenticated)
$routes->match(['get', 'post'], 'homepage/bookings', 'BookingController::bookingsUser', ['filter' => 'auth']); 
$routes->match(['get', 'post'], 'payment/checkout/(:num)', 'PaymentController::paymentUser/$1', ['filter' => 'auth']); 
$routes->match(['get', 'post'], 'payment/processPayment/(:num)', 'PaymentController::processPayment/$1', ['filter' => 'auth']);//Send payment proof
$routes->get('payment/view/(:num)', 'PaymentController::viewPayment/$1', ['filter' => 'auth']);
$routes->get('payment/downloadProof/(:num)', 'PaymentController::downloadProof/$1');

$routes->post('homepage/book', 'BookingController::book', ['filter' => 'auth']); // Post booking request