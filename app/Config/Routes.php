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
$routes->get('/dashboard', 'AdminController::index', ['filter' => 'noauth']); // Dashboard home
$routes->get('dashboard/bookings/(:num)', 'BookingController::bookingsAdmin/$1', ['filter' => 'noauth']); // View bookings (paginated)
$routes->match(['get', 'post'],'dashboard/vehicles/(:num)', 'VehiclesController::index/$1', ['filter' => 'noauth']); // Vehicles management (paginated), handles adding of vehicle
$routes->match(['get', 'post'], 'dashboard/routes/(:num)', 'RoutesController::index/$1', ['filter' => 'noauth']); // Routes management (paginated), handles adding of route
$routes->get('dashboard/schedules/(:num)', 'ScheduleController::index/$1', ['filter' => 'noauth']); // Paginated schedules
$routes->get('dashboard/payments/(:num)', 'PaymentController::index/$1');

// Route Manipulation
$routes->match(['get', 'post'], 'dashboard/routes/view/(:num)', 'RoutesController::viewRoute/$1', ['filter' => 'noauth']); // View route details
$routes->post('dashboard/routes/delete/(:num)', 'RoutesController::deleteRoute/$1', ['filter' => 'noauth']);           // Delete route

// Schedule Creation
$routes->match(['get', 'post'], 'dashboard/schedules/create', 'ScheduleController::create', ['filter' => 'noauth']); // Create new schedule
$routes->get('dashboard/schedule/view/(:num)', 'ScheduleController::view/$1', ['filter' => 'noauth']); //View

//Payments
$routes->get('dashboard/payment/view/(:num)', 'PaymentController::viewPaymentAdmin/$1', ['filter' => 'auth']);
$routes->post('payment/approve/(:num)', 'PaymentController::approve/$1');
$routes->post('payment/reject/(:num)', 'PaymentController::reject/$1');

//Bookings
$routes->get('dashboard/bookings/approve/(:num)', 'BookingController::approve/$1');
$routes->get('dashboard/bookings/decline/(:num)', 'BookingController::decline/$1');


// --- API Routes ---
$routes->get('routes/get/(:any)', 'RoutesController::getRoutes/$1', ['filter' => 'noauth']); // Fetch route data by ID
$routes->get('vehicles/get/(:any)', 'VehiclesController::getVehicles/$1', ['filter' => 'noauth']); // Fetch vehicle data by ID
$routes->get('stops/get/(:num)', 'RoutesController::getStops/$1', ['filter' => 'noauth']); // Get stops associated with a route
$routes->get('stops/search/(:any)', 'RoutesController::searchStop/$1', ['filter' => 'noauth']); // Search stops by name
$routes->get('vehicles/type/get/(:any)', 'VehiclesController::getVehiclesType/$1', ['filter' => 'noauth']); // Get vehicles by type

// --- User Profile and Logout ---
$routes->get('/profile', 'User::profile', ['filter' => 'auth']); // User profile (requires authentication)
$routes->get('/logout', 'User::logout'); // User logout

// --- User Booking Routes ---
$routes->match(['get', 'post'], 'homepage', 'BookingController::index', ['filter' => 'auth']); // Booking homepage (authenticated)
$routes->match(['get', 'post'], 'homepage/bookings', 'BookingController::bookingsUser', ['filter' => 'auth']); // User's booking page
$routes->match(['get', 'post'], 'payment/checkout/(:num)', 'PaymentController::paymentUser/$1', ['filter' => 'auth']); // User Payment
$routes->match(['get', 'post'], 'payment/processPayment/(:num)', 'PaymentController::processPayment/$1', ['filter' => 'auth']);//Send payment proof
$routes->get('payment/view/(:num)', 'PaymentController::viewPayment/$1', ['filter' => 'auth']);
$routes->get('payment/downloadProof/(:num)', 'PaymentController::downloadProof/$1');

$routes->post('homepage/book', 'BookingController::book', ['filter' => 'auth']); // Post booking request
