<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection
 */

$routes->get('/', 'Home::index');

$routes->get('/login', 'Auth::login');               
$routes->post('/auth/loginPost', 'Auth::loginPost');    
$routes->get('/logout', 'Auth::logout');                

$routes->get('/register', 'Register::index');          
$routes->post('/register/save', 'Register::save');     

$routes->get('/dashboard', 'Dashboard::index');      

$routes->get('/jobs', 'Jobs::index');          
$routes->get('/jobs/view/(:num)', 'Jobs::view/$1');    

$routes->get('applications/apply/(:any)', 'Applications::apply/$1');
$routes->post('applications/apply/(:any)', 'Applications::submit/$1');

$routes->get('account/personal', 'Account::personal');   
$routes->post('account/update', 'Account::update');          
$routes->get('account/changePassword', 'Account::changePassword'); 
$routes->post('account/updatePassword', 'Account::updatePassword'); 

$routes->post('applications/apply/(:any)', 'Applications::submit/$1');
$routes->post('applications/submit/(:num)', 'Applications::submit/$1');

$routes->get('applications/view/(:num)', 'Applications::view/$1');
