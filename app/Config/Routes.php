<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

 //Generic Login
$routes->match(['get', 'post'], '/register', 'User::register', ['filter' => 'noauth']);
$routes->match(['get', 'post'], '/login', 'User::login', ['filter' => 'noauth']);           //Currently only redirects to dashboard
$routes->match(['get', 'post'], '/', 'User::login', ['filter' => 'noauth']);                

//Admin Dashboard
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
$routes->get('routes/get/(:any)', 'RoutesController::getRoutes/$1', ['filter' => 'noauth']);
$routes->get('vehicles/get/(:any)', 'VehiclesController::getVehicles/$1', ['filter' => 'noauth']);
$routes->get('stops/get/(:num)', 'RoutesController::getStops/$1', ['filter' => 'noauth']); //Get the stops associated with a route
$routes->get('stops/search/(:any)', 'RoutesController::searchStop/$1', ['filter' => 'noauth']);
$routes->get('vehicles/type/get/(:any)', 'VehiclesController::getVehiclesType/$1', ['filter' => 'noauth']);

//Utils
$routes->get('/profile', 'User::profile', ['filter' => 'auth']);
$routes->get('/logout', 'User::logout');


//For user booking
$routes->match(['get', 'post'],'homepage', 'BookingController::index', ['filter' => 'auth']);
$routes->post('homepage/book', 'BookingController::book', ['filter' => 'auth']);