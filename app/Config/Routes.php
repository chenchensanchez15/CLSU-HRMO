<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection
 */

// Home & Auth
$routes->get('/', 'Home::index');

$routes->get('/login', 'Auth::login');
$routes->post('/auth/loginPost', 'Auth::loginPost');
$routes->get('/logout', 'Auth::logout');

$routes->get('/register', 'Register::index');
$routes->post('/register/save', 'Register::save');

// Dashboard
$routes->get('/dashboard', 'Dashboard::index');

// Jobs
$routes->get('/jobs', 'Jobs::index');
$routes->get('/jobs/view/(:num)', 'Jobs::view/$1');
$routes->get('/jobs/getDetails/(:num)', 'Jobs::getDetails/$1');
$routes->get('/jobs/getAllPosted', 'Jobs::getAllPosted');

// Applications
$routes->get('applications/apply/(:any)', 'Applications::apply/$1');
$routes->post('applications/apply/(:any)', 'Applications::submit/$1'); // form submission from apply page
$routes->post('applications/submit/(:num)', 'Applications::submit/$1'); // redundant? you can keep it if needed

$routes->get('applications/view/(:num)', 'Applications::view/$1');

$routes->post('applications/update/(:num)', 'Applications::update/$1'); // this fixes your 404
$routes->post('applications/withdraw/(:num)', 'Applications::withdraw/$1');

// Documents & Files
$routes->get('applications/viewDocument/(:num)/(:segment)', 'Applications::viewDocument/$1/$2');
$routes->get('applications/getFiles/(:num)', 'Applications::getFiles/$1');
$routes->post('applications/updateFiles', 'Applications::updateFiles');
$routes->get('file/viewDocument/(:num)/(:segment)', 'File::viewDocument/$1/$2');
$routes->get('applications/viewPhoto/(:num)', 'Applications::viewPhoto/$1');
$routes->get('applications/viewResume/(:num)', 'Applications::viewResume/$1');
$routes->get('applications/viewCivilCertificate/(:any)', 'Applications::viewCivilCertificate/$1');

// Account
$routes->get('account/personal', 'Account::personal');
$routes->post('account/update', 'Account::update');
$routes->get('account/changePassword', 'Account::changePassword');
$routes->post('account/updatePassword', 'Account::updatePassword');
$routes->post('account/updatePhoto', 'Account::updatePhoto');
$routes->post('account/updateEducation', 'Account::updateEducation');
$routes->post('account/updateCivilService', 'Account::updateCivilService');
$routes->delete('account/deleteCivilService/(:num)', 'Account::deleteCivilService/$1');
$routes->post('account/updateFiles', 'Account::updateFiles');

// Grouped account routes
$routes->group('account', function($routes) {
    $routes->get('personal', 'Account::personal');
    $routes->post('updateTraining', 'Account::updateTraining');
    $routes->post('updateWorkExperience', 'Account::updateWorkExperience');
    $routes->delete('deleteWorkExperience/(:num)', 'Account::deleteWorkExperience/$1');
});

$routes->post('account/addApplicantTraining', 'Account::addApplicantTraining');
$routes->delete('account/deleteTraining/(:num)', 'Account::deleteTraining/$1');
$routes->get('applications/viewTrainingCertificate/(:num)/(:any)', 'Applications::viewTrainingCertificate/$1/$2');
$routes->get('trainings/certificate/(:any)', 'Account::viewTrainingCertificate/$1');
$routes->get('files/training/(:any)', 'Files::training/$1');
$routes->get('files/document/(:any)', 'Files::document/$1');
$routes->delete('account/deleteEducation/(:num)', 'Account::deleteEducation/$1');
// Family background functionality removed
$routes->get('account/viewCivilCertificate/(:any)', 'Account::viewCivilCertificate/$1');
$routes->get('account/viewTrainingCertificate/(:any)', 'Account::viewTrainingCertificate/$1');
$routes->post('account/updateFile', 'Account::updateFile');
$routes->post('account/deleteFile', 'Account::deleteFile'); // <-- add this
$routes->get('account/viewFile/(:any)', 'Account::viewFile/$1');
$routes->get('account/viewCivilCertificate/(:any)', 'Account::viewCivilCertificate/$1', ['filter' => 'csrf']); // optional filter
$routes->get('file/view-training/(:num)/(:any)', 'File::viewTrainingCertificate/$1/$2');
$routes->get('file/viewFile/(:any)', 'File::viewFile/$1', ['as' => 'viewFile']);
$routes->get('file/view-training/(:num)/(:any)', 'File::viewTraining/$1/$2');
$routes->post('applications/updateFiles', 'Applications::updateFiles');
$routes->get('applications/getFiles/(:num)', 'Applications::getFiles/$1');
