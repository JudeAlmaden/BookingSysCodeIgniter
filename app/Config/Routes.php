<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->match(['get', 'post'], '/register', 'User::register', ['filter' => 'noauth']);
$routes->match(['get', 'post'], '/login', 'User::login', ['filter' => 'noauth']);          
$routes->match(['get', 'post'], '/', 'User::login', ['filter' => 'noauth']);                

$routes->get('/dashboard', 'User::index', ['filter' => 'noauth']);
$routes->get('/profile', 'User::profile', ['filter' => 'auth']);
$routes->get('/logout', 'User::logout');
