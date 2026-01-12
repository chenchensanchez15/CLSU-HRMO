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

$routes->get('register', 'Register::index'); // or whatever controller/method

$routes->get('register', 'Register::index');   // show the registration form
$routes->post('register/save', 'Register::save');  // handle form submission

$routes->get('dashboard', 'Dashboard::index');

$routes->get('/login', 'Auth::login');             // Show login form
$routes->post('/auth/loginPost', 'Auth::loginPost'); // Handle login
$routes->get('/dashboard', 'Dashboard::index');   // Show dashboard
$routes->get('/logout', 'Auth::logout');          // Logout
$routes->get('dashboard', 'Dashboard::index'); // http://localhost:8080/HRMO/dashboard

$routes->get('account/personal', 'Account::personal');
$routes->post('account/update', 'Account::update');
 