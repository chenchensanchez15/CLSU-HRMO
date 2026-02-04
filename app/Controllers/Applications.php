<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException; // make sure this is at the top

use App\Controllers\BaseController;
use App\Models\JobApplicationModel;
use App\Models\JobPositionModel;
use App\Models\ApplicantModel;
use App\Models\ApplicantFamModel;
use App\Models\ApplicantEducationModel;
use App\Models\ApplicantWorkExperienceModel;
use App\Models\ApplicantDocumentsModel; 
use App\Models\ApplicationEducationModel;

class Applications extends BaseController
{
    protected $jobPositions;
    protected $jobApplications;
    protected $applicantPersonal;
    protected $familyModel;
    protected $educationModel;
    protected $workModel;
    protected $documentModel; 

    public function __construct()
    {
        $this->jobPositions = new JobPositionModel();          
        $this->jobApplications = new JobApplicationModel();   
        $this->applicantPersonal = new ApplicantModel();      
        $this->familyModel = new ApplicantFamModel();         
        $this->educationModel = new ApplicantEducationModel();
        $this->workModel = new ApplicantWorkExperienceModel();
        $this->documentModel = new ApplicantDocumentsModel(); 
    }
    
    public function apply($id = null)
{
    if (!$id) return redirect()->to('/jobs');

    $job = $this->jobPositions->find($id);
    if (!$job) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Job not found');

    $user_id = session()->get('user_id');
    if (!$user_id) return redirect()->to('/login');

    $profile = $this->applicantPersonal->where('user_id', $user_id)->first();

    // Fetch family info
    $db = \Config\Database::connect();
    $family = $db->table('applicant_fam')
                 ->where('user_id', $user_id)
                 ->get()
                 ->getResultArray();

    $spouse = $father = $mother = [];
    foreach ($family as $fam) {
        switch ($fam['relationship']) {
            case 'Spouse': $spouse = $fam; break;
            case 'Father': $father = $fam; break;
            case 'Mother': $mother = $fam; break;
        }
    }

    return view('apply', [
        'job'     => $job,
        'profile' => $profile,
        'spouse'  => $spouse,
        'father'  => $father,
        'mother'  => $mother
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
    // INSERT INTO application_personal
    // =========================
    $db->table('application_personal')->insert([
        'job_application_id' => $application_id,
        'first_name' => $this->request->getPost('first_name'),
        'middle_name' => $this->request->getPost('middle_name'),
        'last_name' => $this->request->getPost('last_name'),
        'extension' => $this->request->getPost('extension'),
        'sex' => $this->request->getPost('sex'),
        'date_of_birth' => $this->request->getPost('date_of_birth'),
        'civil_status' => $this->request->getPost('civil_status'),
        'email' => $this->request->getPost('email'),
        'phone' => $this->request->getPost('phone'),
        'citizenship' => $this->request->getPost('citizenship'),
        'residential_address' => $this->request->getPost('residential_address') ?? '-',
        'permanent_address' => $this->request->getPost('permanent_address') ?? '-',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    // =========================
    // INSERT INTO application_fam
    // =========================
    $familyMembers = [
        'Spouse' => [
            'first_name' => $this->request->getPost('spouse_first_name'),
            'middle_name' => $this->request->getPost('spouse_middle_name'),
            'last_name' => $this->request->getPost('spouse_surname'),
            'extension' => $this->request->getPost('spouse_ext_name'),
            'occupation' => $this->request->getPost('spouse_occupation'),
            'contact_no' => $this->request->getPost('spouse_contact')
        ],
        'Father' => [
            'first_name' => $this->request->getPost('father_first_name'),
            'middle_name' => $this->request->getPost('father_middle_name'),
            'last_name' => $this->request->getPost('father_surname'),
            'extension' => $this->request->getPost('father_ext_name'),
            'occupation' => $this->request->getPost('father_occupation') ?? '-',
            'contact_no' => $this->request->getPost('father_contact') ?? '-'
        ],
        'Mother' => [
            'first_name' => $this->request->getPost('mother_first_name'),
            'middle_name' => $this->request->getPost('mother_middle_name'),
            'last_name' => $this->request->getPost('mother_maiden_surname'),
            'extension' => null,
            'occupation' => $this->request->getPost('mother_occupation') ?? '-',
            'contact_no' => $this->request->getPost('mother_contact') ?? '-'
        ]
    ];

    foreach ($familyMembers as $relationship => $member) {
        $member['job_application_id'] = $application_id;
        $member['relationship'] = $relationship;
        $member['created_at'] = date('Y-m-d H:i:s');
        $member['updated_at'] = date('Y-m-d H:i:s');
        $db->table('application_fam')->insert($member);
    }

    // =========================
    // INSERT INTO application_education
    // =========================
    $educationLevels = [
        'Elementary' => 'elementary',
        'Secondary' => 'secondary',
        'Vocational / Trade' => 'vocational',
        'College' => 'college',
        'Graduate Studies' => 'graduate'
    ];

    foreach ($educationLevels as $level => $key) {
        $school = $this->request->getPost($key.'_school');
        $degree = $this->request->getPost($key.'_degree');

        if ($school || $degree) {
            $db->table('application_education')->insert([
                'job_application_id' => $application_id,
                'level' => $level,
                'school_name' => $school ?: 'N/A',
                'degree_course' => $degree ?: 'N/A',
                'period_from' => $this->request->getPost($key.'_period_from') ?: 'N/A',
                'period_to' => $this->request->getPost($key.'_period_to') ?: 'N/A',
                'highest_level_units' => $this->request->getPost($key.'_units') ?: 'N/A',
                'year_graduated' => $this->request->getPost($key.'_year') ?: 'N/A',
                'awards' => $this->request->getPost($key.'_awards') ?: 'N/A',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
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
    $eligibilities = $this->request->getPost('eligibility') ?? [];
    $ratings = $this->request->getPost('rating') ?? [];
    $exam_dates = $this->request->getPost('date_of_exam') ?? [];
    $exam_places = $this->request->getPost('place_of_exam') ?? [];
    $license_nos = $this->request->getPost('license_no') ?? [];
    $license_valid_until = $this->request->getPost('license_valid_until') ?? [];

    for ($i = 0; $i < count($eligibilities); $i++) {
        if (!empty($eligibilities[$i])) {
            $db->table('application_civil_service')->insert([
                'job_application_id' => $application_id,
                'eligibility' => $eligibilities[$i] ?: 'N/A',
                'rating' => $ratings[$i] ?: 'N/A',
                'date_of_exam' => !empty($exam_dates[$i]) ? date('Y-m-d', strtotime($exam_dates[$i])) : null,
                'place_of_exam' => $exam_places[$i] ?? 'N/A',
                'license_no' => $license_nos[$i] ?? 'N/A',
                'license_valid_until' => !empty($license_valid_until[$i]) ? date('Y-m-d', strtotime($license_valid_until[$i])) : null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
// =========================
// INSERT INTO application_trainings
// =========================
$training_categories   = $this->request->getPost('training_category_id') ?? [];
$training_names        = $this->request->getPost('training_name') ?? [];
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
$files = ['resume','tor','diploma','certificate'];

$writablePath = WRITEPATH . 'uploads/';
$publicPath   = FCPATH . 'uploads/';

$docData = [];

foreach ($files as $fileInput) {

    $file     = $this->request->getFile($fileInput);
    $oldFile  = $this->request->getPost('existing_' . $fileInput);

    // 1️⃣ NEW UPLOAD
    if ($file && $file->isValid() && !$file->hasMoved()) {

        $newName = time() . '_' . $file->getRandomName();
        $file->move($writablePath, $newName);
        $docData[$fileInput] = $newName;
    }

    // 2️⃣ KEEP OLD FILE
    elseif (!empty($oldFile)) {

        if (file_exists($writablePath . $oldFile)) {
            $docData[$fileInput] = $oldFile;
        }
        elseif (file_exists($publicPath . $oldFile)) {
            copy($publicPath . $oldFile, $writablePath . $oldFile);
            $docData[$fileInput] = $oldFile;
        }
        else {
            $docData[$fileInput] = null;
        }
    }

    else {
        $docData[$fileInput] = null;
    }
}

// INSERT PER-APPLICATION SNAPSHOT
$db->table('application_documents')->insert([
    'job_application_id' => $application_id,
    'resume'      => $docData['resume'],
    'tor'         => $docData['tor'],
    'diploma'     => $docData['diploma'],
    'certificate' => $docData['certificate'],
    'uploaded_at' => date('Y-m-d H:i:s'),
    'created_at'  => date('Y-m-d H:i:s'),
    'updated_at'  => date('Y-m-d H:i:s')
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

    // -------------------------
    // Fetch family background
    // -------------------------
    $app['family'] = $db->table('application_fam')
                        ->where('job_application_id', $application_id)
                        ->get()
                        ->getResultArray() ?? [];

    foreach ($app['family'] as &$fam) {
        $middle = (!empty($fam['middle_name']) && strtoupper($fam['middle_name']) !== 'N/A') 
                    ? strtoupper(substr($fam['middle_name'], 0, 1)) . '.' 
                    : '';
        $suffix = (!empty($fam['extension']) && strtoupper($fam['extension']) !== 'N/A') 
                    ? $fam['extension'] 
                    : '';
        $first = !empty($fam['first_name']) ? ucfirst(strtolower($fam['first_name'])) : '';
        $last  = !empty($fam['last_name']) ? ucfirst(strtolower($fam['last_name'])) : '';
        $fam['full_name_formatted'] = implode(' ', array_filter([$first, $middle, $last, $suffix])) ?: '-';
        $fam['occupation_formatted'] = (!empty($fam['occupation']) && strtoupper($fam['occupation']) !== 'N/A') 
                                        ? ucwords(strtolower($fam['occupation'])) : '-';
        $fam['contact_formatted'] = (!empty($fam['contact_no']) && strtoupper($fam['contact_no']) !== 'N/A') 
                                        ? $fam['contact_no'] : '-';
    }

    // -------------------------
    // Fetch education
    // -------------------------
    $app['education'] = $db->table('application_education')
                           ->where('job_application_id', $application_id)
                           ->orderBy('id_application_education', 'ASC')
                           ->get()
                           ->getResultArray() ?? [];
    foreach ($app['education'] as &$edu) {
        $from = $edu['period_from'] ?? '-';
        $to   = $edu['period_to'] ?? '-';
        $edu['period'] = $from . ' - ' . $to;
        if (!empty($edu['school_name'])) $edu['school_name'] = ucwords(strtolower($edu['school_name']));
        if (!empty($edu['level'])) $edu['level'] = ucwords(strtolower($edu['level']));
        if (!empty($edu['highest_level_units'])) $edu['highest_level_units'] = ucwords(strtolower($edu['highest_level_units']));
        if (!empty($edu['awards'])) $edu['awards'] = ucwords(strtolower($edu['awards']));
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

// =========================
// Family Background (all rows from application_fam)
// =========================
$family = $db->table('application_fam')
    ->where('job_application_id', $application_id)
    ->orderBy('id_application_fam', 'ASC')
    ->get()
    ->getResultArray();


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
        'family'          => $family,   // <-- pass all family rows
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
    // Update Personal Information
    // -------------------
    $personalData = [
        'first_name'          => $this->request->getPost('first_name') ?: '-',
        'middle_name'         => $this->request->getPost('middle_name') ?: '-',
        'last_name'           => $this->request->getPost('last_name') ?: '-',
        'extension'           => $this->request->getPost('name_extension') ?: '-',
        'date_of_birth'       => $this->request->getPost('birth_date') ?: null,
        'sex'                 => $this->request->getPost('sex') ?: '-',
        'civil_status'        => $this->request->getPost('civil_status') ?: '-',
        'citizenship'         => $this->request->getPost('citizenship') ?: '-',
        'email'               => $this->request->getPost('email') ?: '-',
        'phone'               => $this->request->getPost('phone') ?: '-',
        'residential_address' => $this->request->getPost('residential_address') ?: '-',
        'permanent_address'   => $this->request->getPost('permanent_address') ?: '-',
        'updated_at'          => $currentDate
    ];

    $db->table('application_personal')
       ->where('job_application_id', $job_application_id)
       ->update($personalData);

    // -------------------
    // Update Family Background
    // -------------------
    $famTable = $db->table('application_fam');
    $famIds = $this->request->getPost('fam_last_name') ?? [];

    foreach ($famIds as $id => $lastName) {
        $data = [
            'first_name'  => $this->request->getPost('fam_first_name')[$id] ?? '-',
            'middle_name' => $this->request->getPost('fam_middle_name')[$id] ?? '-',
            'last_name'   => $lastName ?: '-',
            'extension'   => $this->request->getPost('fam_extension')[$id] ?? '-',
            'occupation'  => $this->request->getPost('fam_occupation')[$id] ?? '-',
            'contact_no'  => $this->request->getPost('fam_contact_no')[$id] ?? null,
            'updated_at'  => $currentDate
        ];

        if (!empty($data['contact_no'])) {
            $data['contact_no'] = preg_replace('/\D/', '', $data['contact_no']);
            $data['contact_no'] = substr($data['contact_no'], 0, 11);
        }

        if ($id) {
            // Update existing row
            $famTable->where('id_application_fam', $id)->update($data);
        } else {
            // Insert new row
            $data['job_application_id'] = $job_application_id;
            $data['relationship'] = $this->request->getPost('fam_relationship')[$id] ?? 'Unknown';
            $data['created_at'] = $currentDate;
            $famTable->insert($data);
        }
    }

    // -------------------
    // Update Educational Background
    // -------------------
    $eduLevels = [
        'Elementary'        => 'elementary',
        'Secondary'         => 'secondary',
        'Vocational / Trade'=> 'vocational',
        'College'           => 'college',
        'Graduate Studies'  => 'graduate'
    ];
    $eduTable = $db->table('application_education');

    foreach ($eduLevels as $level => $key) {
        $data = [
            'school_name'        => $this->request->getPost($key.'_school') ?: '-',
            'degree_course'      => $this->request->getPost($key.'_degree') ?: '-',
            'period_from'        => $this->request->getPost($key.'_period_from') ?: null,
            'period_to'          => $this->request->getPost($key.'_period_to') ?: null,
            'highest_level_units'=> $this->request->getPost($key.'_units') ?: '-',
            'year_graduated'     => $this->request->getPost($key.'_year') ?: '-',
            'awards'             => $this->request->getPost($key.'_awards') ?: '-',
            'updated_at'         => $currentDate
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
    $positions = $this->request->getPost('position_title') ?? [];
    $offices   = $this->request->getPost('office') ?? [];
    $dates_from = $this->request->getPost('date_from') ?? [];
    $dates_to   = $this->request->getPost('date_to') ?? [];
    $statuses   = $this->request->getPost('status_of_appointment') ?? [];
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
    // Update Civil Service
    // -------------------
    $csTable = $db->table('application_civil_service');
    $eligibilities = $this->request->getPost('eligibility') ?? [];
    $ratings       = $this->request->getPost('rating') ?? [];
    $dates_exam    = $this->request->getPost('date_of_exam') ?? [];
    $places_exam   = $this->request->getPost('place_of_exam') ?? [];
    $license_nos   = $this->request->getPost('license_no') ?? [];
    $valid_untils  = $this->request->getPost('license_valid_until') ?? [];

    $csTable->where('job_application_id', $job_application_id)->delete();

    for ($i = 0; $i < count($eligibilities); $i++) {
        if (
            empty($eligibilities[$i]) &&
            empty($ratings[$i]) &&
            empty($dates_exam[$i]) &&
            empty($places_exam[$i]) &&
            empty($license_nos[$i]) &&
            empty($valid_untils[$i])
        ) continue;

        $csTable->insert([
            'job_application_id' => $job_application_id,
            'eligibility'        => $eligibilities[$i] ?: '-',
            'rating'             => $ratings[$i] ?: '-',
            'date_of_exam'       => !empty($dates_exam[$i]) ? date('Y-m-d', strtotime($dates_exam[$i])) : null,
            'place_of_exam'      => $places_exam[$i] ?: '-',
            'license_no'         => $license_nos[$i] ?: '-',
            'license_valid_until'=> !empty($valid_untils[$i]) ? date('Y-m-d', strtotime($valid_untils[$i])) : null,
            'created_at'         => $currentDate,
            'updated_at'         => $currentDate
        ]);
    }

// -------------------
// Update Trainings
// -------------------
$training_ids          = $this->request->getPost('training_id') ?? [];
$training_categories   = $this->request->getPost('training_category_id') ?? [];
$training_names        = $this->request->getPost('training_name') ?? [];
$training_from         = $this->request->getPost('training_date_from') ?? [];
$training_to           = $this->request->getPost('training_date_to') ?? [];
$training_facilitators = $this->request->getPost('training_facilitator') ?? [];
$training_hours        = $this->request->getPost('training_hours') ?? [];
$training_sponsors     = $this->request->getPost('training_sponsor') ?? [];
$training_remarks      = $this->request->getPost('training_remarks') ?? [];
$existingFiles         = $this->request->getPost('existing_certificate_file') ?? [];

$trainTable = $db->table('application_trainings');
$writablePath = WRITEPATH . 'uploads/trainings/';
$currentDate = date('Y-m-d H:i:s');

// Get all uploaded files as array
$uploadedFiles = $this->request->getFileMultiple('training_certificate');

$totalRows = count($training_names);

for ($i = 0; $i < $totalRows; $i++) {

    // Skip empty training name
    if (empty(trim($training_names[$i] ?? ''))) continue;

    $certificateFile = $existingFiles[$i] ?? null;

    // Handle uploaded file
    if (isset($uploadedFiles[$i]) && $uploadedFiles[$i]->isValid() && !$uploadedFiles[$i]->hasMoved()) {
        $certificateFile = time() . '_' . $uploadedFiles[$i]->getRandomName();
        $uploadedFiles[$i]->move($writablePath, $certificateFile);
    }

    $data = [
        'training_category_id' => $training_categories[$i] ?? 1,
        'training_name'        => $training_names[$i],
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
        // Update existing row
        $trainTable->where('id_application_trainings', $training_ids[$i])->update($data);
    } else {
        // Insert new row
        $data['job_application_id'] = $job_application_id;
        $data['added_date']         = date('Y-m-d');
        $data['created_at']         = $currentDate;
        $trainTable->insert($data);
    }
}

// -------------------
// Delete Trainings if removed
// -------------------
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
$files = ['resume', 'tor', 'diploma', 'certificate'];
$uploadPath = WRITEPATH . 'uploads/'; // <-- save directly here
$docTable = $db->table('application_documents');

// Fetch existing documents for this application
$existingDocs = $docTable->where('job_application_id', $job_application_id)->get()->getRowArray() ?? [];

$docData = [];
$currentDate = date('Y-m-d H:i:s');

foreach ($files as $fileInput) {
    $file = $this->request->getFile($fileInput);
    $oldFile = $this->request->getPost('existing_' . $fileInput);

    if ($file && $file->isValid() && !$file->hasMoved()) {
        // Save new uploaded file directly in writable/uploads/
        $newName = time() . '_' . $file->getRandomName();
        $file->move($uploadPath, $newName);
        $docData[$fileInput] = $newName;
        $docData['uploaded_at'] = $currentDate;
    } elseif (!empty($oldFile)) {
        // Keep existing file if present
        $docData[$fileInput] = $oldFile;
    } else {
        $docData[$fileInput] = null;
    }
}

$docData['updated_at'] = $currentDate;

if ($existingDocs) {
    // Update existing row
    $docTable->where('job_application_id', $job_application_id)->update($docData);
} else {
    // Insert new row
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
    $filePath = WRITEPATH . 'uploads/' . $file;

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

public function viewTrainingCertificate($application_id, $filename)
{
    // Sanitize the filename to prevent path traversal
    $filename = basename($filename);

    // Full path to the file in writable/uploads/trainings/
    $filePath = WRITEPATH . 'uploads/trainings/' . $filename;

    // Check if the file exists
    if (!file_exists($filePath)) {
        throw PageNotFoundException::forPageNotFound('Certificate file not found.');
    }

    // Stream the PDF inline so it opens in a new tab
    return $this->response->setHeader('Content-Type', 'application/pdf')
                          ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                          ->setBody(file_get_contents($filePath));
}

}
