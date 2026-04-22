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
$routes->post('/dashboard', 'Dashboard::index');
$routes->post('/dashboard/pagination', 'Dashboard::pagination');

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

// Google Drive OAuth
$routes->get('google/drive', 'Google::drive');
$routes->get('google/callback', 'Google::callback');
$routes->get('google/callback/', 'Google::callback'); // Handle trailing slash

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
$routes->get('account/getProfilePhoto', 'Photo::getProfilePhoto');
$routes->get('account/getProfilePhoto/(:num)', 'Photo::getProfilePhoto/$1');
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
// New routes for multi-document training certificate viewer (actual files, NOT combined)
$routes->get('training-documents/view-multiple/(:num)', 'TrainingDocuments::viewMultiple/$1');
$routes->get('training-documents/view-multiple-by-user/(:any)', 'TrainingDocuments::viewMultipleByUser/$1');
$routes->get('training-documents/get-certificate/(:any)', 'TrainingDocuments::getCertificate/$1');
// Route for user's own multiple training certificates in profile
$routes->get('account/view-multiple-training-certificates', 'Account::viewMultipleTrainingCertificates');
$routes->get('account/viewCombinedTrainingCertificates', 'Account::viewCombinedTrainingCertificates');
$routes->get('applications/viewCombinedTrainingCertificates/(:num)', 'Applications::viewCombinedTrainingCertificates/$1');
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
$routes->get('account/viewEligibilityCertificates', 'Account::viewEligibilityCertificates');
$routes->get('account/viewTrainingCertificates', 'Account::viewTrainingCertificates');

// Google Auth
$routes->get('google/redirectToGoogle', 'GoogleAuth::redirectToGoogle');
$routes->get('google/callback', 'GoogleAuth::handleCallback');
$routes->get('google/revokeAccess', 'GoogleAuth::revokeAccess');

// Job Vacancies
$routes->get('job-vacancies', 'JobVacancies::index');
$routes->get('job-vacancies/create', 'JobVacancies::create');
$routes->post('job-vacancies', 'JobVacancies::store');
$routes->get('job-vacancies/(:num)', 'JobVacancies::show/$1');
$routes->get('job-vacancies/(:num)/edit', 'JobVacancies::edit/$1');
$routes->put('job-vacancies/(:num)', 'JobVacancies::update/$1');
$routes->delete('job-vacancies/(:num)', 'JobVacancies::delete/$1');
$routes->get('job-vacancies/search', 'JobVacancies::search');

