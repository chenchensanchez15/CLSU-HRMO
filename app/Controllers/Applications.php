<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException; // make sure this is at the top

use App\Controllers\BaseController;
use App\Models\JobApplicationModel;
use App\Models\JobPositionModel;
use App\Models\ApplicantModel;

use App\Models\ApplicantEducationModel;
use App\Models\ApplicantWorkExperienceModel;
use App\Models\ApplicantDocumentsModel; 
use App\Models\ApplicationEducationModel;
use App\Models\ApplicationCivilServiceModel;

class Applications extends BaseController
{
    protected $jobPositions;
    protected $jobApplications;
    protected $applicantPersonal;
    protected $educationModel;
    protected $workModel;
    protected $documentModel; 
    protected $civilServiceModel; 

    public function __construct()
    {
        $this->jobPositions = new JobPositionModel();          
        $this->jobApplications = new JobApplicationModel();   
        $this->applicantPersonal = new ApplicantModel();      
        // Family background functionality removed
        $this->educationModel = new ApplicantEducationModel();
        $this->workModel = new ApplicantWorkExperienceModel();
        $this->documentModel = new ApplicantDocumentsModel(); 
        $this->civilServiceModel = new ApplicationCivilServiceModel();
    }
    
    public function apply($id = null)
{
    if (!$id) return redirect()->to('/jobs');

    $job = $this->jobPositions->find($id);
    if (!$job) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Job not found');

    $user_id = session()->get('user_id');
    if (!$user_id) {
        // Store the job ID in session for redirect after login
        session()->set('redirect_after_login', '/applications/apply/' . $id);
        return redirect()->to('/login');
    }

    // Check if user already applied for this job
    $db = \Config\Database::connect();
    $existingApplication = $db->table('job_applications')
        ->where([
            'user_id' => $user_id,
            'job_vacancy_id' => $id
        ])
        ->whereIn('application_status', ['Submitted', 'Submitted. For Evaluation', 'Under Review', 'Shortlisted', 'For Interview'])
        ->get()
        ->getRow();

    if ($existingApplication) {
        // User already applied - show SweetAlert and redirect to dashboard
        session()->setFlashdata('already_applied', true);
        session()->setFlashdata('job_title', $job['position_title']);
        return redirect()->to('/dashboard');
    }

    $profile = $this->applicantPersonal->where('user_id', $user_id)->first();

    return view('apply', [
        'job'     => $job,
        'profile' => $profile
    ]);
}

public function submit($id = null)
{
    $job = $this->jobPositions->find($id);
    if (!$job) {
        return $this->response->setStatusCode(404)->setBody('Job not found');
    }

    $user_id = session()->get('user_id');
    if (!$user_id) {
        return $this->response->setStatusCode(403)->setBody('User not logged in');
    }

    $db = \Config\Database::connect();

    // =========================
    // INSERT INTO job_applications
    // =========================
    $this->jobApplications->insert([
        'user_id' => $user_id,
        'job_vacancy_id' => $job['id'],
        'application_status' => 'Submitted',
        'applied_at' => date('Y-m-d H:i:s'),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
    $application_id = $this->jobApplications->getInsertID();

    // =========================
    // INSERT INTO application_personal - DIRECT DATABASE COPY
    // =========================
    
    // ALWAYS fetch fresh data from applicant_personal table
    $applicantPersonal = $this->applicantPersonal->where('user_id', $user_id)->first();
    
    if (!$applicantPersonal || empty($applicantPersonal['first_name'])) {
        return $this->response->setStatusCode(400)->setBody('Personal information not found in your profile. Please complete your profile in the Personal Information section first.');
    }
    
    // Use database values directly - ignore any potentially problematic POST data
    $firstName = $applicantPersonal['first_name'];
    $middleName = $applicantPersonal['middle_name'] ?? '';
    $lastName = $applicantPersonal['last_name'] ?? '';
    $extension = $applicantPersonal['suffix'] ?? '';
    $sex = $applicantPersonal['sex'] ?? '';
    $dateOfBirth = $applicantPersonal['date_of_birth'] ?? '';
    $civilStatus = $applicantPersonal['civil_status'] ?? '';
    $email = $applicantPersonal['email'] ?? '';
    $phone = $applicantPersonal['phone'] ?? '';
    $citizenship = $applicantPersonal['citizenship'] ?? '';
    $residentialAddress = $applicantPersonal['residential_address'] ?? '';
    $permanentAddress = $applicantPersonal['permanent_address'] ?? '';
    
    $db->table('application_personal')->insert([
        'job_application_id' => $application_id,
        'first_name' => $firstName,
        'middle_name' => $middleName,
        'last_name' => $lastName,
        'extension' => $extension,
        'sex' => $sex,
        'date_of_birth' => $dateOfBirth,
        'civil_status' => $civilStatus,
        'email' => $email,
        'phone' => $phone,
        'citizenship' => $citizenship,
        'residential_address' => $residentialAddress ?: '-',
        'permanent_address' => $permanentAddress ?: '-',
        'is_clsu_employee' => $this->request->getPost('is_clsu_employee') ?? 'No',
        'clsu_employee_specify' => $this->request->getPost('clsu_employee_specify') ?? null,
        'religion' => $this->request->getPost('religion') ?? null,
        'is_indigenous' => $this->request->getPost('is_indigenous') ?? 'No',
        'indigenous_specify' => $this->request->getPost('indigenous_specify') ?? null,
        'is_pwd' => $this->request->getPost('is_pwd') ?? 'No',
        'pwd_specify' => $this->request->getPost('pwd_specify') ?? null,
        'is_solo_parent' => $this->request->getPost('is_solo_parent') ?? 'No',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    // =========================
    // INSERT INTO application_education
    // =========================
    $user_id = session()->get('user_id');
    $educationRecords = $db->table('applicant_education')
                         ->where('user_id', $user_id)
                         ->orderBy('degree_level_id', 'ASC')
                         ->get()
                         ->getResultArray();
    
    foreach ($educationRecords as $edu) {
        $db->table('application_education')->insert([
            'job_application_id' => $application_id,
            'degree_level_id' => $edu['degree_level_id'] ?? null,
            'degree_id' => $edu['degree_id'] ?? null,
            'level' => '', // Will be populated from lib_degree_level
            'school_name' => $edu['school_name'] ?? 'N/A',
            'degree_course' => $edu['degree_course'] ?? 'N/A',
            'course' => $edu['course'] ?? 'N/A',
            'period_from' => $edu['period_from'] ?? 'N/A',
            'period_to' => $edu['period_to'] ?? 'N/A',
            'highest_level_units' => $edu['highest_level_units'] ?? 'N/A',
            'year_graduated' => $edu['year_graduated'] ?? 'N/A',
            'awards' => $edu['awards'] ?? 'N/A',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    // =========================
    // INSERT INTO application_work_experience
    // =========================
    $positions = $this->request->getPost('position_title') ?? [];
    $offices = $this->request->getPost('office') ?? [];
    $dates_from = $this->request->getPost('date_from') ?? [];
    $dates_to = $this->request->getPost('date_to') ?? [];
    $statuses = $this->request->getPost('status_of_appointment') ?? [];
    $govt_services = $this->request->getPost('govt_service') ?? [];

    for ($i = 0; $i < count($positions); $i++) {
        $db->table('application_work_experience')->insert([
            'job_application_id' => $application_id,
            'position_title' => $positions[$i] ?? 'N/A',
            'office' => $offices[$i] ?? 'N/A',
            'date_from' => !empty($dates_from[$i]) ? date('Y-m-d', strtotime($dates_from[$i])) : null,
            'date_to' => !empty($dates_to[$i]) ? date('Y-m-d', strtotime($dates_to[$i])) : null,
            'status_of_appointment' => $statuses[$i] ?? 'N/A',
            'govt_service' => (isset($govt_services[$i]) && $govt_services[$i] === 'Yes') ? 'Yes' : 'No',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

// =========================
// INSERT INTO application_civil_service
// =========================
// Get civil service data from form submission
$eligibilities = $this->request->getPost('eligibility') ?? [];
$ratings = $this->request->getPost('rating') ?? [];
$exam_dates = $this->request->getPost('date_of_exam') ?? [];
$exam_places = $this->request->getPost('place_of_exam') ?? [];
$license_nos = $this->request->getPost('license_no') ?? [];
$valid_until_dates = $this->request->getPost('license_valid_until') ?? [];

// Process only the civil service records that were submitted in the form
$totalCivilRecords = count($eligibilities);
for ($i = 0; $i < $totalCivilRecords; $i++) {
    // Skip if eligibility is empty (deleted rows won't be submitted)
    if (empty(trim($eligibilities[$i]))) {
        continue;
    }
    
    // Get the corresponding record from applicant_civil_service table
    $user_id = session()->get('user_id');
    $cs_record = $db->table('applicant_civil_service')
                   ->where('user_id', $user_id)
                   ->where('eligibility', $eligibilities[$i])
                   ->where('rating', $ratings[$i])
                   ->orderBy('date_of_exam', 'DESC')
                   ->get()
                   ->getRowArray();
    
    if (!$cs_record) {
        continue; // Skip if record not found
    }
    
    $cs = $cs_record;
    // Copy certificate file if it exists
    $certificateName = null;
    if (!empty($cs['certificate'])) {
        $sourcePath = WRITEPATH . 'uploads/civil_service/' . $cs['certificate'];
        if (file_exists($sourcePath)) {
            $certificateName = $cs['certificate'];
            // Copy file to application-specific location
            $destinationPath = WRITEPATH . 'uploads/civil_service/' . $certificateName;
            if (!file_exists($destinationPath)) {
                copy($sourcePath, $destinationPath);
            }
        }
    }

    // Insert into application_civil_service table
    $db->table('application_civil_service')->insert([
        'job_application_id' => $application_id,
        'eligibility' => $cs['eligibility'] ?? 'N/A',
        'rating' => $cs['rating'] ?? 'N/A',
        'date_of_exam' => !empty($cs['date_of_exam']) ? date('Y-m-d', strtotime($cs['date_of_exam'])) : null,
        'place_of_exam' => $cs['place_of_exam'] ?? 'N/A',
        'license_no' => $cs['license_no'] ?? 'N/A',
        'license_valid_until' => !empty($cs['license_valid_until']) ? date('Y-m-d', strtotime($cs['license_valid_until'])) : null,
        'certificate' => $certificateName,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
}

// =========================
// INSERT INTO application_trainings
// =========================
$training_categories   = $this->request->getPost('training_category_id') ?? [];
$training_names        = $this->request->getPost('training_name') ?? [];
$training_venues       = $this->request->getPost('training_venue') ?? [];
$training_from         = $this->request->getPost('training_date_from') ?? [];
$training_to           = $this->request->getPost('training_date_to') ?? [];
$training_facilitators = $this->request->getPost('training_facilitator') ?? [];
$training_hours        = $this->request->getPost('training_hours') ?? [];
$training_sponsors     = $this->request->getPost('training_sponsor') ?? [];
$training_remarks      = $this->request->getPost('training_remarks') ?? [];
$existingFiles         = $this->request->getPost('existing_certificate_file') ?? [];
$uploadedFiles         = $this->request->getFileMultiple('training_certificate') ?? [];

$writablePath = WRITEPATH . 'uploads/trainings/';
$publicPath   = FCPATH . 'uploads/';

$totalRows = count($training_names);

for ($i = 0; $i < $totalRows; $i++) {

    if (empty(trim($training_names[$i] ?? ''))) {
        continue;
    }

    $certificateFile = null;

    // 1️⃣ NEW upload → writable/uploads/trainings
    if (
        isset($uploadedFiles[$i]) &&
        $uploadedFiles[$i]->isValid() &&
        !$uploadedFiles[$i]->hasMoved()
    ) {
        $certificateFile = $uploadedFiles[$i]->getRandomName();
        $uploadedFiles[$i]->move($writablePath, $certificateFile);
    }

    // 2️⃣ PREFILLED certificate → COPY public → writable
    elseif (!empty($existingFiles[$i])) {

        $oldFile = $existingFiles[$i];

        // already exists in writable
        if (file_exists($writablePath . $oldFile)) {
            $certificateFile = $oldFile;
        }
        // copy from public/uploads → writable/uploads/trainings
        elseif (file_exists($publicPath . $oldFile)) {
            copy($publicPath . $oldFile, $writablePath . $oldFile);
            $certificateFile = $oldFile;
        }
    }

    $db->table('application_trainings')->insert([
        'job_application_id'   => $application_id,
        'training_category_id' => $training_categories[$i] ?? 1,
        'training_name'        => $training_names[$i],
        'training_venue'       => $training_venues[$i] ?? 'N/A',
        'date_from'            => !empty($training_from[$i])
                                    ? date('Y-m-d', strtotime($training_from[$i]))
                                    : null,
        'date_to'              => !empty($training_to[$i])
                                    ? date('Y-m-d', strtotime($training_to[$i]))
                                    : null,
        'training_facilitator' => $training_facilitators[$i] ?? 'N/A',
        'training_hours'       => $training_hours[$i] ?? 0,
        'training_sponsor'     => $training_sponsors[$i] ?? 'N/A',
        'training_remarks'     => $training_remarks[$i] ?? 'N/A',
        'certificate_file'     => $certificateFile,
        'added_date'           => date('Y-m-d H:i:s'),
        'created_at'           => date('Y-m-d H:i:s'),
        'updated_at'           => date('Y-m-d H:i:s')
    ]);
}
// =========================
// FILE UPLOADS: application_documents (FINAL FIX)
// =========================
$files = ['pds','performance_rating','resume','tor','diploma'];

$writablePath = WRITEPATH . 'uploads/files/';
$publicPath   = FCPATH . 'uploads/';

$docData = [
    'pds' => null,
    'performance_rating' => null,
    'resume' => null,
    'tor' => null,
    'diploma' => null
];

// Handle existing files first (READ-ONLY scenario)
foreach ($files as $fileInput) {
    $oldFile = $this->request->getPost('existing_' . $fileInput);
    
    if (!empty($oldFile)) {
        // Check if file exists in writable directory
        if (file_exists($writablePath . $oldFile)) {
            if ($fileInput === 'pds') {
                $docData['pds'] = $oldFile;
            } elseif ($fileInput === 'performance_rating') {
                $docData['performance_rating'] = $oldFile;
            } else {
                $docData[$fileInput] = $oldFile;
            }
        }
        // Check if file exists in public directory and copy it
        elseif (file_exists($publicPath . $oldFile)) {
            copy($publicPath . $oldFile, $writablePath . $oldFile);
            if ($fileInput === 'pds') {
                $docData['pds'] = $oldFile;
            } elseif ($fileInput === 'performance_rating') {
                $docData['performance_rating'] = $oldFile;
            } else {
                $docData[$fileInput] = $oldFile;
            }
        }
    }
}

// Handle new file uploads (if any)
foreach ($files as $fileInput) {
    $file = $this->request->getFile($fileInput);
    
    // Only process if it's a valid new upload
    if ($file && $file->isValid() && !$file->hasMoved()) {
        $newName = time() . '_' . $file->getRandomName();
        $file->move($writablePath, $newName);
        
        // Map to correct database field names
        if ($fileInput === 'pds') {
            $docData['pds'] = $newName;
        } elseif ($fileInput === 'performance_rating') {
            $docData['performance_rating'] = $newName;
        } else {
            $docData[$fileInput] = $newName;
        }
    }
}

// INSERT PER-APPLICATION SNAPSHOT
$db->table('application_documents')->insert([
    'job_application_id' => $application_id,
    'pds'              => $docData['pds'],
    'performance_rating' => $docData['performance_rating'],
    'resume'           => $docData['resume'],
    'tor'              => $docData['tor'],
    'diploma'          => $docData['diploma'],
    'uploaded_at'      => date('Y-m-d H:i:s'),
    'created_at'       => date('Y-m-d H:i:s'),
    'updated_at'       => date('Y-m-d H:i:s')
]);

    return $this->response->setJSON([
        'success' => true,
        'application_id' => $application_id
    ]);
}

public function view($application_id = null)
{
    if (!$application_id) {
        return redirect()->to('applications');
    }

    $db = \Config\Database::connect();

    // -------------------------
    // Fetch the application
    // -------------------------
    $app = $this->jobApplications->find($application_id);
    if (!$app) {
        return $this->response->setStatusCode(404)->setBody('Application not found');
    }

    // -------------------------
    // Fetch personal information
    // -------------------------
    $app['personal'] = $db->table('application_personal')
                           ->where('job_application_id', $application_id)
                           ->get()
                           ->getRowArray() ?? [];

    $app['personal']['date_of_birth_formatted'] = !empty($app['personal']['date_of_birth'])
        ? date('F d, Y', strtotime($app['personal']['date_of_birth']))
        : '-';

    // Family background functionality removed

    // -------------------------
    // Fetch education with joins
    // -------------------------
    $app['education'] = $db->table('application_education ae')
                           ->select('ae.*, ddl.degree_level_name, ld.degree_name')
                           ->join('lib_degree_level ddl', 'ae.degree_level_id = ddl.id_degree_level', 'left')
                           ->join('lib_degrees ld', 'ae.degree_id = ld.id_degree', 'left')
                           ->where('ae.job_application_id', $application_id)
                           ->orderBy('ddl.id_degree_level', 'ASC')
                           ->get()
                           ->getResultArray() ?? [];
    
    // Group education by level
    $educationByLevel = [];
    $allLevels = [
        1 => 'Elementary',
        2 => 'Secondary', 
        3 => 'Vocational / Trade Course',
        4 => 'College',
        5 => 'Graduate Studies',
        6 => 'Doctorate'
    ];
    
    // Initialize all levels
    foreach ($allLevels as $levelId => $levelName) {
        $educationByLevel[$levelName] = [];
    }
    
    // Group existing records by level
    foreach ($app['education'] as $edu) {
        $levelName = $edu['degree_level_name'] ?? 'Unknown';
        if (isset($educationByLevel[$levelName])) {
            $educationByLevel[$levelName][] = $edu;
        }
    }
    
    // Reformat for view
    $app['education_display'] = [];
    foreach ($allLevels as $levelId => $levelName) {
        if (!empty($educationByLevel[$levelName])) {
            foreach ($educationByLevel[$levelName] as $index => $edu) {
                $app['education_display'][] = [
                    'level' => $index === 0 ? $levelName : '', // Show level name only on first row
                    'school_name' => !empty($edu['school_name']) && strtoupper($edu['school_name']) !== 'N/A' ? $edu['school_name'] : '-',
                    'degree_course' => !empty($edu['degree_name']) ? $edu['degree_name'] : (!empty($edu['degree_course']) && strtoupper($edu['degree_course']) !== 'N/A' ? $edu['degree_course'] : '-'),
                    'course' => !empty($edu['course']) && strtoupper($edu['course']) !== 'N/A' ? $edu['course'] : '-',
                    'period_from' => !empty($edu['period_from']) && strtoupper($edu['period_from']) !== 'N/A' ? $edu['period_from'] : '-',
                    'period_to' => !empty($edu['period_to']) && strtoupper($edu['period_to']) !== 'N/A' ? $edu['period_to'] : '-',
                    'highest_level_units' => !empty($edu['highest_level_units']) && strtoupper($edu['highest_level_units']) !== 'N/A' ? $edu['highest_level_units'] : '-',
                    'year_graduated' => !empty($edu['year_graduated']) && strtoupper($edu['year_graduated']) !== 'N/A' ? $edu['year_graduated'] : '-',
                    'awards' => !empty($edu['awards']) && strtoupper($edu['awards']) !== 'N/A' ? $edu['awards'] : '-'
                ];
            }
        } else {
            // Add empty row for level with no records
            $app['education_display'][] = [
                'level' => $levelName,
                'school_name' => '-',
                'degree_course' => '-',
                'period_from' => '-',
                'period_to' => '-',
                'highest_level_units' => '-',
                'year_graduated' => '-',
                'awards' => '-'
            ];
        }
    }

    // -------------------------
    // Fetch civil service records
    // -------------------------
    $app['civil'] = $db->table('application_civil_service')
                        ->where('job_application_id', $application_id)
                        ->get()
                        ->getResultArray() ?? [];

    foreach ($app['civil'] as &$cs) {
        $cs['date_of_exam'] = !empty($cs['date_of_exam']) ? date('F d, Y', strtotime($cs['date_of_exam'])) : '-';
        $cs['license_valid_until'] = !empty($cs['license_valid_until']) ? date('F d, Y', strtotime($cs['license_valid_until'])) : '-';
        $cs['eligibility'] = !empty($cs['eligibility']) && strtoupper($cs['eligibility']) !== 'N/A' ? $cs['eligibility'] : '-';
        $cs['rating'] = !empty($cs['rating']) && strtoupper($cs['rating']) !== 'N/A' ? $cs['rating'] : '-';
        $cs['place_of_exam'] = !empty($cs['place_of_exam']) && strtoupper($cs['place_of_exam']) !== 'N/A' ? $cs['place_of_exam'] : '-';
        $cs['license_no'] = !empty($cs['license_no']) && strtoupper($cs['license_no']) !== 'N/A' ? $cs['license_no'] : '-';
    }// -------------------------
// Fetch work experience
// -------------------------
$app['work'] = $db->table('application_work_experience')
                  ->where('job_application_id', $application_id)
                  ->orderBy('date_from', 'DESC')
                  ->get()
                  ->getResultArray() ?? [];

foreach ($app['work'] as &$work) {
    // Format inclusive dates
    $from = (!empty($work['date_from']) && $work['date_from'] !== '0000-00-00') 
        ? date('F d, Y', strtotime($work['date_from'])) 
        : '-';
    $to = (!empty($work['date_to']) && $work['date_to'] !== '0000-00-00') 
        ? date('F d, Y', strtotime($work['date_to'])) 
        : 'Present';
    $work['inclusive_dates'] = $from . ' - ' . $to;

    // Govt service as stored in DB (Yes/No)
    $work['govt_service'] = (isset($work['govt_service']) && strtoupper($work['govt_service']) === 'YES') ? 'Yes' : 'No';

    // Default empty fields
    $work['position_title']       = !empty($work['position_title']) ? $work['position_title'] : '-';
    $work['office']               = !empty($work['office']) ? $work['office'] : '-';
    $work['status_of_appointment'] = !empty($work['status_of_appointment']) ? $work['status_of_appointment'] : '-';
}
unset($work);

    // -------------------------
    // Fetch uploaded documents
    // -------------------------
    $app['documents'] = $db->table('application_documents')
                           ->where('job_application_id', $application_id)
                           ->get()
                           ->getRowArray() ?? [
                               'resume'      => null,
                               'tor'         => null,
                               'diploma'     => null,
                               'certificate' => null,
                               'uploaded_at' => null
                           ];

    // -------------------------
    // Fetch job details
    // -------------------------
    $job = $db->table('job_vacancies')
              ->where('id', $app['job_vacancy_id'])
              ->get()
              ->getRowArray() ?? [
                  'position_title' => '-',
                  'office'         => '-',
                  'department'     => '-',
                  'monthly_salary' => 0,
                  'application_deadline' => null
              ];

    // -------------------------
    // Fetch applicant profile
    // -------------------------
    $profileModel = new \App\Models\ApplicantModel();
    $profile = $profileModel->where('user_id', $app['user_id'])->first() ?? [];

// -------------------------
// Fetch trainings
// -------------------------
$trainings = [];
$user_id = $profile['user_id'] ?? $app['user_id'] ?? null;
if ($user_id) {
    $trainings = $db->table('application_trainings at')
                    ->join('lib_training_category tc', 'at.training_category_id = tc.id_training_category', 'left')
                    ->select('at.training_name, at.date_from, at.date_to, at.training_facilitator, at.training_hours, at.training_sponsor, at.training_remarks, at.certificate_file, tc.training_category_name')
                    ->where(['at.job_application_id' => $application_id]) // filter by application
                    ->orderBy('at.date_from', 'DESC')
                    ->get()
                    ->getResultArray();

    // Format dates
    foreach ($trainings as &$tr) {
        $tr['date_from'] = !empty($tr['date_from']) ? date('F d, Y', strtotime($tr['date_from'])) : '-';
        $tr['date_to']   = !empty($tr['date_to']) ? date('F d, Y', strtotime($tr['date_to'])) : '-';
    }
}

    // -------------------------
    // Profile photo
    // -------------------------
    $profilePhoto = null;
    if (!empty($profile['photo'])) {
        $photoPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $profile['photo'];
        if (file_exists($photoPath)) {
            $profilePhoto = $profile['photo'];
        }
    }

    // -------------------------
    // Pass all data to the view
    // -------------------------
    return view('applications/view', [
        'app'          => $app,
        'job'          => $job,
        'profile'      => $profile,
        'profilePhoto' => $profilePhoto,
        'trainings'    => $trainings
    ]);
}

public function edit($application_id = null)
{
    if (!$application_id) {
        return redirect()->to('applications');
    }

    $db = \Config\Database::connect();

    // =========================
    // Main application
    // =========================
    $app = $this->jobApplications
        ->where('id_job_application', $application_id)
        ->first();

    if (!$app) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Application not found');
    }

    // =========================
    // Personal Information
    // =========================
    $personal = $db->table('application_personal')
        ->where('job_application_id', $application_id)
        ->orderBy('updated_at', 'DESC')
        ->limit(1)
        ->get()
        ->getRowArray() ?? [];

// Family background functionality removed

    // =========================
    // Educational Background
    // =========================
    $educationRows = $db->table('application_education')
        ->where('job_application_id', $application_id)
        ->get()
        ->getResultArray();

    $defaultEdu = [
        'id_application_education' => '',
        'school_name' => 'N/A',
        'degree_course' => 'N/A',
        'period_from' => 'N/A',
        'period_to' => 'N/A',
        'highest_level_units' => 'N/A',
        'year_graduated' => 'N/A',
        'awards' => 'N/A',
    ];

    $education_data = [
        'elementary' => $defaultEdu,
        'secondary'  => $defaultEdu,
        'vocational' => $defaultEdu,
        'college'    => $defaultEdu,
        'graduate'   => $defaultEdu,
    ];

    foreach ($educationRows as $edu) {
        $edu = array_merge($defaultEdu, $edu);
        switch (strtolower(trim($edu['level']))) {
            case 'elementary': $education_data['elementary'] = $edu; break;
            case 'secondary':
            case 'high school': $education_data['secondary'] = $edu; break;
            case 'vocational / trade': $education_data['vocational'] = $edu; break;
            case 'college': $education_data['college'] = $edu; break;
            case 'graduate studies': $education_data['graduate'] = $edu; break;
        }
    }

    $elementary  = $education_data['elementary'];
    $highschool  = $education_data['secondary'];
    $vocational  = $education_data['vocational'];
    $college     = $education_data['college'];
    $graduate    = $education_data['graduate'];

    // =========================
    // Work Experience
    // =========================
    $applicant_work = $db->table('application_work_experience')
        ->where('job_application_id', $application_id)
        ->orderBy('date_from', 'DESC')
        ->get()
        ->getResultArray();

    // =========================
    // Civil Service Eligibility
    // =========================
    $civil_services = $db->table('application_civil_service')
        ->where('job_application_id', $application_id)
        ->orderBy('date_of_exam', 'DESC')
        ->get()
        ->getResultArray();

    // =========================
    // Trainings / Seminars / Workshops
    // =========================
    $trainings = $db->table('application_trainings at')
        ->select('at.*, tc.training_category_name')
        ->join('lib_training_category tc', 'tc.id_training_category = at.training_category_id', 'left')
        ->where('at.job_application_id', $application_id)
        ->orderBy('at.date_from', 'DESC')
        ->get()
        ->getResultArray();

    $categories = $db->table('lib_training_category')->get()->getResultArray();

    // =========================
    // Documents
    // =========================
    $documents = $db->table('application_documents')
        ->where('job_application_id', $application_id)
        ->orderBy('uploaded_at', 'DESC')
        ->limit(1)
        ->get()
        ->getRowArray() ?? [];

    // =========================
    // Job Info & Profile
    // =========================
    $job = $db->table('job_vacancies')
        ->where('id', $app['job_vacancy_id'])
        ->get()
        ->getRowArray() ?? [];

    $profileModel = new \App\Models\ApplicantModel();
    $profile = $profileModel->where('user_id', $app['user_id'])->first() ?? [];

    // =========================
    // Return view
    // =========================
    return view('applications/edit', [
        'app'             => $app,
        'job'             => $job,
        'profile'         => $profile,
        'personal'        => $personal,
        // Family background functionality removed
        'elementary'      => $elementary,
        'highschool'      => $highschool,
        'vocational'      => $vocational,
        'college'         => $college,
        'graduate'        => $graduate,
        'applicant_work'  => $applicant_work,
        'civil_services'  => $civil_services,
        'trainings'       => $trainings,
        'categories'      => $categories,
        'documents'       => $documents
    ]);
}

public function update($job_application_id = null)
{
    if (!$job_application_id) {
        return redirect()->to('applications');
    }

    $db = \Config\Database::connect();
    $currentDate = date('Y-m-d H:i:s');

    // -------------------
    // Update Personal Information + Additional Personal Details
    // -------------------
$personalData = [
    'first_name'             => $this->request->getPost('first_name') ?: '-',
    'middle_name'            => $this->request->getPost('middle_name') ?: '-',
    'last_name'              => $this->request->getPost('last_name') ?: '-',
    'extension'              => $this->request->getPost('extension') ?: '-',
    'date_of_birth'          => $this->request->getPost('date_of_birth') ?: null,
    'sex'                    => $this->request->getPost('sex') ?: '-',
    'civil_status'           => $this->request->getPost('civil_status') ?: '-',
    'citizenship'            => $this->request->getPost('citizenship') ?: '-',
    'email'                  => $this->request->getPost('email') ?: '-',
    'phone'                  => $this->request->getPost('phone') ?: '-',
    'residential_address'    => $this->request->getPost('residential_address') ?: '-',
    'permanent_address'      => $this->request->getPost('permanent_address') ?: '-',

    // Additional Personal Details
    'is_clsu_employee'       => $this->request->getPost('is_clsu_employee') ?: 'No',
    'clsu_employee_specify'  => ($this->request->getPost('is_clsu_employee') === 'Yes') ? $this->request->getPost('clsu_employee_specify') ?: '-' : null,
    'religion'               => $this->request->getPost('religion') ?: '-',

    'is_indigenous'          => $this->request->getPost('is_indigenous') ?: 'No',
    'indigenous_specify'     => ($this->request->getPost('is_indigenous') === 'Yes') ? $this->request->getPost('indigenous_specify') ?: '-' : null,

    'is_pwd'                 => $this->request->getPost('is_pwd') ?: 'No',
    'pwd_specify'            => ($this->request->getPost('is_pwd') === 'Yes') ? $this->request->getPost('pwd_specify') ?: '-' : null,

    'is_solo_parent'         => $this->request->getPost('is_solo_parent') ?: 'No',

    'updated_at'             => date('Y-m-d H:i:s')
];

$db->table('application_personal')
   ->where('job_application_id', $job_application_id)
   ->update($personalData);

   // Family background functionality removed

    // -------------------
    // Update Educational Background
    // -------------------
    $eduLevels = [
        'Elementary'         => 'elementary',
        'Secondary'          => 'secondary',
        'Vocational / Trade' => 'vocational',
        'College'            => 'college',
        'Graduate Studies'   => 'graduate'
    ];
    $eduTable = $db->table('application_education');

    foreach ($eduLevels as $level => $key) {
        $data = [
            'school_name'         => $this->request->getPost($key.'_school') ?: '-',
            'degree_course'       => $this->request->getPost($key.'_degree') ?: '-',
            'period_from'         => $this->request->getPost($key.'_period_from') ?: null,
            'period_to'           => $this->request->getPost($key.'_period_to') ?: null,
            'highest_level_units' => $this->request->getPost($key.'_units') ?: '-',
            'year_graduated'      => $this->request->getPost($key.'_year') ?: '-',
            'awards'              => $this->request->getPost($key.'_awards') ?: '-',
            'updated_at'          => $currentDate
        ];

        $existing = $eduTable->where('job_application_id', $job_application_id)
                             ->where('level', $level)
                             ->get()
                             ->getRowArray();

        if ($existing) {
            $eduTable->where('id_application_education', $existing['id_application_education'])
                     ->update($data);
        } else {
            $data['job_application_id'] = $job_application_id;
            $data['level'] = $level;
            $data['created_at'] = $currentDate;
            $eduTable->insert($data);
        }
    }

    // -------------------
    // Update Work Experience
    // -------------------
    $positions    = $this->request->getPost('position_title') ?? [];
    $offices      = $this->request->getPost('office') ?? [];
    $dates_from   = $this->request->getPost('date_from') ?? [];
    $dates_to     = $this->request->getPost('date_to') ?? [];
    $statuses     = $this->request->getPost('status_of_appointment') ?? [];
    $govt_services = $this->request->getPost('govt_service') ?? [];

    $db->table('application_work_experience')->where('job_application_id', $job_application_id)->delete();

    for ($i = 0; $i < count($positions); $i++) {
        $workData = [
            'job_application_id'    => $job_application_id,
            'position_title'        => $positions[$i] ?: 'N/A',
            'office'                => $offices[$i] ?: 'N/A',
            'date_from'             => !empty($dates_from[$i]) ? date('Y-m-d', strtotime($dates_from[$i])) : null,
            'date_to'               => !empty($dates_to[$i]) ? date('Y-m-d', strtotime($dates_to[$i])) : null,
            'status_of_appointment' => $statuses[$i] ?: 'N/A',
            'govt_service'          => in_array($govt_services[$i] ?? '', ['Yes','No']) ? $govt_services[$i] : 'No',
            'created_at'            => $currentDate,
            'updated_at'            => $currentDate
        ];
        $db->table('application_work_experience')->insert($workData);
    }

// -------------------
// Update Civil Service Records
// -------------------
$user_id = session()->get('user_id');
$civilServices = $db->table('applicant_civil_service')
                   ->where('user_id', $user_id)
                   ->orderBy('date_of_exam', 'DESC')
                   ->get()
                   ->getResultArray();

$civilTable = $db->table('application_civil_service');

// Delete existing records for this application
$civilTable->where('job_application_id', $job_application_id)->delete();

foreach ($civilServices as $cs) {
    // Copy certificate file if it exists
    $certificateName = null;
    if (!empty($cs['certificate'])) {
        $sourcePath = WRITEPATH . 'uploads/civil_service/' . $cs['certificate'];
        if (file_exists($sourcePath)) {
            $certificateName = $cs['certificate'];
            // Copy file to application-specific location
            $destinationPath = WRITEPATH . 'uploads/civil_service/' . $certificateName;
            if (!file_exists($destinationPath)) {
                copy($sourcePath, $destinationPath);
            }
        }
    }

    // Insert into application_civil_service table
    $civilTable->insert([
        'job_application_id' => $job_application_id,
        'eligibility'        => $cs['eligibility'] ?? 'N/A',
        'rating'             => $cs['rating'] ?? 'N/A',
        'date_of_exam'       => !empty($cs['date_of_exam']) ? date('Y-m-d', strtotime($cs['date_of_exam'])) : null,
        'place_of_exam'      => $cs['place_of_exam'] ?? 'N/A',
        'license_no'         => $cs['license_no'] ?? 'N/A',
        'license_valid_until'=> !empty($cs['license_valid_until']) ? date('Y-m-d', strtotime($cs['license_valid_until'])) : null,
        'certificate'        => $certificateName,
        'created_at'         => $currentDate,
        'updated_at'         => $currentDate
    ]);
}



    // -------------------
    // Update Trainings
    // -------------------
   // -------------------
// Update Trainings
// -------------------
$training_ids          = $this->request->getPost('training_id') ?? [];
$training_categories   = $this->request->getPost('training_category_id') ?? [];
$training_names        = $this->request->getPost('training_name') ?? [];
$training_from         = $this->request->getPost('training_date_from') ?? [];
$training_to           = $this->request->getPost('training_date_to') ?? [];
$training_venues       = $this->request->getPost('training_venue') ?? []; // <-- added
$training_facilitators = $this->request->getPost('training_facilitator') ?? [];
$training_hours        = $this->request->getPost('training_hours') ?? [];
$training_sponsors     = $this->request->getPost('training_sponsor') ?? [];
$training_remarks      = $this->request->getPost('training_remarks') ?? [];
$existingFiles         = $this->request->getPost('existing_certificate_file') ?? [];

$trainTable = $db->table('application_trainings');
$writablePath = WRITEPATH . 'uploads/trainings/';
$uploadedFiles = $this->request->getFileMultiple('training_certificate');
$totalRows = count($training_names);

for ($i = 0; $i < $totalRows; $i++) {
    if (empty(trim($training_names[$i] ?? ''))) continue;

    $certificateFile = $existingFiles[$i] ?? null;

    if (isset($uploadedFiles[$i]) && $uploadedFiles[$i]->isValid() && !$uploadedFiles[$i]->hasMoved()) {
        $certificateFile = time() . '_' . $uploadedFiles[$i]->getRandomName();
        $uploadedFiles[$i]->move($writablePath, $certificateFile);
    }

    $data = [
        'training_category_id' => $training_categories[$i] ?? 1,
        'training_name'        => $training_names[$i],
        'training_venue'       => $training_venues[$i] ?? 'N/A', // <-- added
        'date_from'            => !empty($training_from[$i]) ? date('Y-m-d', strtotime($training_from[$i])) : null,
        'date_to'              => !empty($training_to[$i]) ? date('Y-m-d', strtotime($training_to[$i])) : null,
        'training_facilitator' => $training_facilitators[$i] ?? 'N/A',
        'training_hours'       => $training_hours[$i] ?? 0,
        'training_sponsor'     => $training_sponsors[$i] ?? 'N/A',
        'training_remarks'     => $training_remarks[$i] ?? 'N/A',
        'certificate_file'     => $certificateFile,
        'updated_at'           => $currentDate
    ];

    if (!empty($training_ids[$i])) {
        $trainTable->where('id_application_trainings', $training_ids[$i])->update($data);
    } else {
        $data['job_application_id'] = $job_application_id;
        $data['added_date']         = date('Y-m-d');
        $data['created_at']         = $currentDate;
        $trainTable->insert($data);
    }
}

$deletedTrainings = $this->request->getPost('deleted_training_ids');
if (!empty($deletedTrainings)) {
    $deletedIds = array_filter(explode(',', $deletedTrainings));
    if (!empty($deletedIds)) {
        $trainTable->whereIn('id_application_trainings', $deletedIds)->delete();
    }
}

    // -------------------
    // Update Documents
    // -------------------
   // Include PDS and Performance Rating
$files = ['pds', 'performance_rating', 'resume', 'tor', 'diploma', 'certificate'];
$uploadPath = WRITEPATH . 'uploads/files/'; // ensure correct path
$docTable = $db->table('application_documents');

$existingDocs = $docTable->where('job_application_id', $job_application_id)->get()->getRowArray() ?? [];
$docData = [];
$currentDate = date('Y-m-d H:i:s');

foreach ($files as $fileInput) {
    $file = $this->request->getFile($fileInput);
    $oldFile = $this->request->getPost('existing_' . $fileInput);

    if ($file && $file->isValid() && !$file->hasMoved()) {
        $newName = time() . '_' . $file->getRandomName();
        $file->move($uploadPath, $newName);
        $docData[$fileInput] = $newName;
        $docData['uploaded_at'] = $currentDate;
    } elseif (!empty($oldFile)) {
        $docData[$fileInput] = $oldFile;
    } else {
        $docData[$fileInput] = null;
    }
}

$docData['updated_at'] = $currentDate;

if ($existingDocs) {
    $docTable->where('job_application_id', $job_application_id)->update($docData);
} else {
    $docData['job_application_id'] = $job_application_id;
    $docData['created_at'] = $currentDate;
    $docTable->insert($docData);
}


    return redirect()->to('dashboard')->with('success', 'Application updated successfully!');
}


public function withdraw($id = null)
{
    if (!$this->request->isAJAX()) {
        return redirect()->to(site_url('dashboard'));
    }

    if (!$id) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid application ID.'
        ]);
    }

    // Load your model (example: ApplicationsModel)
    $applicationModel = new \App\Models\JobApplicationModel();

    // Update status to "Withdrawn application"
    $updated = $applicationModel->update($id, ['application_status' => 'Withdrawn application']);

    if ($updated) {
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Application withdrawn successfully.'
        ]);
    } else {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update application.'
        ]);
    }
}

public function viewDocument($application_id, $doc)
{
    $session = session();
    $current_user_id = $session->get('user_id'); // Get logged-in user ID

    // 1️⃣ Block if not logged in
    if (!$current_user_id) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Unauthorized access');
    }

    $db = \Config\Database::connect();

$record = $db->table('application_documents')
             ->where('job_application_id', $application_id) // ✅ correct column
             ->get()
             ->getRowArray();


    if (!$record || empty($record[$doc])) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
    }

    // 3️⃣ Optional: Check if the document belongs to this user
    $app_owner_id = $record['user_id'] ?? null; // Make sure your table has user_id
    if ($app_owner_id && $app_owner_id != $current_user_id) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Unauthorized access');
    }

    // 4️⃣ File path
    $file = $record[$doc];
    $filePath = WRITEPATH . 'uploads/files/' . $file;

    if (!file_exists($filePath)) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('File does not exist on server.');
    }

    // 5️⃣ Stream the file inline
    $mime = mime_content_type($filePath) ?: 'application/octet-stream';
    return $this->response->setHeader('Content-Type', $mime)
                          ->setHeader('Content-Disposition', 'inline; filename="' . basename($file) . '"')
                          ->setBody(file_get_contents($filePath));
}

public function viewResume($profile_id)
{
    $db = \Config\Database::connect();

    // Fetch the resume from applicant_profiles
    $profile = $db->table('applicant_personal')
                  ->select('resume, user_id')
                  ->where('id', $profile_id)
                  ->get()
                  ->getRowArray();

    if (!$profile || empty($profile['resume'])) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Resume not uploaded');
    }

    // 🔒 Ensure the current user owns this resume
    $current_user_id = session()->get('user_id');
    if (!$current_user_id || $current_user_id != $profile['user_id']) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Unauthorized access');
    }

    // File path
    $file = $profile['resume'];
    $filePath = FCPATH . 'uploads/' . $file;

    // Check if file exists
    if (!file_exists($filePath)) {
        // Try fallback to WRITEPATH in case it was uploaded there
        $filePath = WRITEPATH . 'uploads/' . $file;
        if (!file_exists($filePath)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Resume file not found on server');
        }
    }

    // Stream file inline
    $mime = mime_content_type($filePath) ?: 'application/pdf';
    return $this->response->setHeader('Content-Type', $mime)
                          ->setHeader('Content-Disposition', 'inline; filename="' . basename($file) . '"')
                          ->setBody(file_get_contents($filePath));
}


public function viewPhoto($user_id)
{
    $current_user_id = session()->get('user_id'); // Check logged-in user

    // 1️⃣ Block if not logged in
    if (!$current_user_id) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Unauthorized access');
    }

    $db = \Config\Database::connect();

    // 2️⃣ Fetch the user's photo
    $profile = $db->table('applicant_personal')
                  ->select('photo, user_id')
                  ->where('user_id', $user_id)
                  ->get()
                  ->getRowArray();

    if (!$profile || empty($profile['photo'])) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Photo not found');
    }

    // 3️⃣ Optional: ensure the current user is allowed to view this photo
    if ($current_user_id != $profile['user_id']) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Unauthorized access');
    }

    // 4️⃣ File path
    $filePath = FCPATH . 'uploads/' . $profile['photo'];

    if (!file_exists($filePath)) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Photo file missing');
    }

    // 5️⃣ Stream photo to browser
    $mime = mime_content_type($filePath) ?: 'image/jpeg';
    return $this->response->setHeader('Content-Type', $mime)
                          ->setHeader('Content-Disposition', 'inline; filename="' . basename($profile['photo']) . '"')
                          ->setBody(file_get_contents($filePath));
}

 public function viewTrainingCertificate($id, $filename)
    {
        $filename = basename($filename); // sanitize
        $filePath = WRITEPATH . 'uploads/trainings/' . $filename;

        if (!file_exists($filePath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Certificate not found.');
        }

        return $this->response
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                    ->setBody(file_get_contents($filePath));
    }

public function updateFiles()
{
    $application_id = $this->request->getPost('job_application_id');
    
    if (!$application_id) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Application ID is required.'
        ]);
    }
    
    $db = \Config\Database::connect();
    $uploadPath = WRITEPATH . 'uploads/files/';
    
    // Get existing documents
    $existingDocs = $db->table('application_documents')
        ->where('job_application_id', $application_id)
        ->get()
        ->getRowArray() ?? [];
    
    $docData = [];
    $currentDate = date('Y-m-d H:i:s');
    
    // Handle file uploads for each document type
    $files = ['pds', 'performance_rating', 'resume', 'tor', 'diploma'];
    
    foreach ($files as $fileInput) {
        $file = $this->request->getFile($fileInput);
        $oldFile = $existingDocs[$fileInput] ?? null;
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Upload new file
            $newName = time() . '_' . $file->getRandomName();
            $file->move($uploadPath, $newName);
            $docData[$fileInput] = $newName;
        } elseif (!empty($oldFile)) {
            // Keep existing file
            $docData[$fileInput] = $oldFile;
        } else {
            // No file
            $docData[$fileInput] = null;
        }
    }
    
    $docData['updated_at'] = $currentDate;
    $docData['uploaded_at'] = $currentDate;
    
    // Update or insert document record
    if ($existingDocs) {
        $db->table('application_documents')
            ->where('job_application_id', $application_id)
            ->update($docData);
    } else {
        $docData['job_application_id'] = $application_id;
        $docData['created_at'] = $currentDate;
        $db->table('application_documents')->insert($docData);
    }
    
    return $this->response->setJSON([
        'success' => true,
        'message' => 'Files updated successfully!'
    ]);
}

public function getFiles($id)
{
    $model = new \App\Models\ApplicantDocumentsModel();
    $files = $model->where('job_application_id', $id)->first();
    
    if (!$files) {
        return $this->response->setJSON([
            'pds' => null,
            'performance_rating' => null,
            'resume' => null,
            'tor' => null,
            'diploma' => null
        ]);
    }
    
    return $this->response->setJSON([
        'pds' => $files['pds'] ? base_url('uploads/files/' . $files['pds']) : null,
        'performance_rating' => $files['performance_rating'] ? base_url('uploads/files/' . $files['performance_rating']) : null,
        'resume' => $files['resume'] ? base_url('uploads/files/' . $files['resume']) : null,
        'tor' => $files['tor'] ? base_url('uploads/files/' . $files['tor']) : null,
        'diploma' => $files['diploma'] ? base_url('uploads/files/' . $files['diploma']) : null,
    ]);
}

public function viewCivilCertificate($filename = null)
{
    if (!$filename) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    // Decode filename from URL
    $filename = urldecode($filename);

    // Adjust path to match your actual folder
    $filePath = WRITEPATH . 'uploads/civil_service/' . $filename;

    if (!file_exists($filePath)) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("File not found: $filename");
    }

    // Determine mime type
    $mime = mime_content_type($filePath);

    // Stream file inline (don't force download)
    return $this->response
        ->setHeader('Content-Type', $mime)
        ->setHeader('Content-Disposition', 'inline; filename="'.$filename.'"')
        ->setBody(file_get_contents($filePath));
}


}
