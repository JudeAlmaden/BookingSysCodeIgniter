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
$routes->post( 'dashboard/routes/delete/(:num)', 'AdminController::RoutesController/$1', ['filter' => 'noauth']);

$routes->match(['get', 'post'],'dashboard/vehicles', 'VehiclesController::index', ['filter' => 'noauth']);
$routes->match(['get', 'post'],'dashboard/vehicles/(:num)', 'VehiclesController::index/$1', ['filter' => 'noauth']);

$routes->get('/profile', 'User::profile', ['filter' => 'auth']);
$routes->get('/logout', 'User::logout');
