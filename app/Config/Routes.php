<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->match(['get', 'post'], '/register', 'User::register', ['filter' => 'noauth']);
$routes->match(['get', 'post'], '/login', 'User::login', ['filter' => 'noauth']);           //Currently only redirects to dashboard
$routes->match(['get', 'post'], '/', 'User::login', ['filter' => 'noauth']);                


$routes->get('/dashboard', 'AdminController::index', ['filter' => 'noauth']);
$routes->match(['get', 'post'], 'dashboard/routes', 'RoutesController::index', ['filter' => 'noauth']);
$routes->match(['get', 'post'], 'dashboard/routes/(:num)', 'RoutesController::routes/$1', ['filter' => 'noauth']);
$routes->match(['get', 'post'], 'dashboard/routes/view/(:num)', 'RoutesController::viewRoute/$1', ['filter' => 'noauth']);
$routes->post('dashboard/routes/delete/(:num)', 'RoutesController::deleteRoute/$1', ['filter' => 'noauth']);

$routes->match(['get', 'post'],'dashboard/vehicles', 'VehiclesController::index', ['filter' => 'noauth']);
$routes->match(['get', 'post'],'dashboard/vehicles/(:num)', 'VehiclesController::index/$1', ['filter' => 'noauth']);

$routes->get('dashboard/schedules', 'ScheduleController::index', ['filter' => 'noauth']);
$routes->match(['get', 'post'],'dashboard/schedules/create', 'ScheduleController::create', ['filter' => 'noauth']);

//For Api call
$routes->get('dashboard/routes/get/(:any)', 'RoutesController::getRoutes/$1', ['filter' => 'noauth']);
$routes->get('dashboard/vehicles/get/(:any)', 'VehiclesController::getVehicles/$1', ['filter' => 'noauth']);
$routes->get('dashboard/stops/get/(:num)', 'RoutesController::getStops/$1', ['filter' => 'noauth']);

//Utils
$routes->get('/profile', 'User::profile', ['filter' => 'auth']);
$routes->get('/logout', 'User::logout');


//For user booking
$routes->get('/homepage', 'BookingController::index', ['filter' => 'noauth']);