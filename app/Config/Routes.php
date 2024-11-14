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

// Route Manipulation
$routes->match(['get', 'post'], 'dashboard/routes/view/(:num)', 'RoutesController::viewRoute/$1', ['filter' => 'noauth']); // View route details
$routes->post('dashboard/routes/delete/(:num)', 'RoutesController::deleteRoute/$1', ['filter' => 'noauth']);           // Delete route

// Schedule Creation
$routes->match(['get', 'post'], 'dashboard/schedules/create', 'ScheduleController::create', ['filter' => 'noauth']); // Create new schedule

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
$routes->post('homepage/book', 'BookingController::book', ['filter' => 'auth']); // Post booking request
