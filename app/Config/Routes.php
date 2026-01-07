<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('/register', function () {
    return view('register');
});
$routes->get('/jobs', function () {
    return view('jobs');
});
$routes->get('/apply', function () {
    return view('apply');
});

$routes->get('jobs', 'Jobs::index');           // List all jobs
$routes->get('jobs/view/(:num)', 'Jobs::view/$1');  // View single job by ID

$routes->get('dashboard', 'Dashboard::index');
