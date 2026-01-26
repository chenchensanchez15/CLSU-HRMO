<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JobApplicationModel;
use App\Models\JobPositionModel;
use App\Models\ApplicantModel;
use App\Models\ApplicantFamModel;
use App\Models\ApplicantEducationModel;
use App\Models\ApplicantWorkExperienceModel;
use App\Models\ApplicantDocumentsModel; 

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

        if (!$job) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Job not found');
        }

        $user_id = session()->get('user_id');
        if (!$user_id) return redirect()->to('/login');

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

    $application_id = $this->jobApplications->getInsertID(); // ID for job_applications

$db->table('application_personal')->insert([
    'job_application_id' => $application_id,
    'first_name' => $this->request->getPost('first_name'),
    'middle_name' => $this->request->getPost('middle_name'), // matches form
    'last_name' => $this->request->getPost('last_name'),
    'extension' => $this->request->getPost('extension'), // match input name
    'sex' => $this->request->getPost('sex'),
    'date_of_birth' => $this->request->getPost('date_of_birth'), // match input name
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
    $from = $this->request->getPost($key.'_period_from');
    $to = $this->request->getPost($key.'_period_to');
    $units = $this->request->getPost($key.'_units');
    $year = $this->request->getPost($key.'_year');
    $awards = $this->request->getPost($key.'_awards');

    // Insert if at least school or degree is filled
    if ($school || $degree) {
        $db->table('application_education')->insert([
            'job_application_id' => $application_id,
            'level' => $level,
            'school_name' => $school ?: 'N/A',
            'degree_course' => $degree ?: 'N/A',
            'period_from' => $from ?: 'N/A',
            'period_to' => $to ?: 'N/A',
            'highest_level_units' => $units ?: 'N/A',
            'year_graduated' => $year ?: 'N/A',
            'awards' => $awards ?: 'N/A',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}

// =========================
// INSERT INTO application_work_experience (multiple entries)
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
        'position_title' => !empty($positions[$i]) ? $positions[$i] : 'N/A',
        'office' => !empty($offices[$i]) ? $offices[$i] : 'N/A',
        'date_from' => !empty($dates_from[$i]) ? date('Y-m-d', strtotime($dates_from[$i])) : '0000-00-00',
        'date_to' => !empty($dates_to[$i]) ? date('Y-m-d', strtotime($dates_to[$i])) : '0000-00-00',
        'status_of_appointment' => !empty($statuses[$i]) ? $statuses[$i] : 'N/A',
        'govt_service' => !empty($govt_services[$i]) ? $govt_services[$i] : 'N/A',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
}

    // =========================
    // FILE UPLOADS: application_documents
    // =========================
    $files = ['resume','tor','diploma','certificate'];
    $uploadedFiles = [];
    $uploadPath = WRITEPATH.'uploads/';

    foreach ($files as $fileInput) {
        $file = $this->request->getFile($fileInput);
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $uploadedFiles[$fileInput] = $newName;
        } else {
            $uploadedFiles[$fileInput] = null;
        }
    }

    $db->table('application_documents')->insert([
        'job_application_id' => $application_id,
        'resume' => $uploadedFiles['resume'],
        'tor' => $uploadedFiles['tor'],
        'diploma' => $uploadedFiles['diploma'],
        'certificate' => $uploadedFiles['certificate'],
        'uploaded_at' => date('Y-m-d H:i:s'),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
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
    // -------------------------
    // Fetch related applicant data
    // -------------------------
    $app['family'] = $db->table('application_fam')
                        ->where('job_application_id', $application_id)
                        ->get()
                        ->getResultArray() ?? [];

    $app['education'] = $db->table('application_education')
                           ->where('job_application_id', $application_id)
                           ->orderBy('id_application_education', 'ASC')
                           ->get()
                           ->getResultArray() ?? [];

    $app['work'] = $db->table('application_work_experience')
                      ->where('job_application_id', $application_id)
                      ->orderBy('date_from', 'DESC')
                      ->get()
                      ->getResultArray() ?? []; // fetch all work experiences

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
    $applicant_id = $profile['id'] ?? null;
    if ($applicant_id) {
        $trainings = $db->table('applicant_trainings at')
                        ->join('trainings t', 'at.training_id = t.id_training')
                        ->join('lib_training_category tc', 't.training_category_id = tc.id_training_category')
                        ->select('at.training_hours, at.training_sponsor, at.training_remarks, at.training_certificate_file, t.training_name, t.training_facilitator, t.training_datefrom, t.training_dateto, tc.training_category_name')
                        ->where('at.applicant_id', $applicant_id)
                        ->orderBy('t.training_datefrom', 'DESC')
                        ->get()
                        ->getResultArray();

        // Format dates
        foreach ($trainings as &$tr) {
            $tr['training_datefrom'] = !empty($tr['training_datefrom']) ? date('F d, Y', strtotime($tr['training_datefrom'])) : '-';
            $tr['training_dateto']   = !empty($tr['training_dateto']) ? date('F d, Y', strtotime($tr['training_dateto'])) : '-';
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
    $app = $this->jobApplications->find($application_id);
    if (!$app) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Application not found');
    }

    // =========================
    // Family Background
    // =========================
    $family = $db->table('application_fam')
                 ->where('job_application_id', $application_id)
                 ->get()
                 ->getResultArray();

    $spouse = $father = $mother = [];
    foreach ($family as $fam) {
        if ($fam['relationship'] === 'Spouse') {
            $spouse = $fam;
        } elseif ($fam['relationship'] === 'Father') {
            $father = $fam;
        } elseif ($fam['relationship'] === 'Mother') {
            $mother = $fam;
        }
    }

    // =========================
    // Educational Background
    // =========================
    $educationRows = $db->table('application_education')
                        ->where('job_application_id', $application_id)
                        ->get()
                        ->getResultArray();

    $education_data = [
        'elementary' => [],
        'secondary' => [],
        'vocational' => [],
        'college' => [],
        'graduate' => []
    ];

    foreach ($educationRows as $edu) {
        switch ($edu['level']) {
            case 'Elementary':
                $education_data['elementary'] = $edu;
                break;
            case 'Secondary':
            case 'High School':
                $education_data['secondary'] = $edu;
                break;
            case 'Vocational/Trade':
                $education_data['vocational'] = $edu;
                break;
            case 'College':
                $education_data['college'] = $edu;
                break;
            case 'Graduate Studies':
                $education_data['graduate'] = $edu;
                break;
        }
    }

    // =========================
    // Work Experience
    // =========================
    $applicant_work = $db->table('application_work_experience')
                         ->where('job_application_id', $application_id)
                         ->get()
                         ->getRowArray();

    // =========================
    // Documents
    // =========================
    $documents = $db->table('application_documents')
                    ->where('job_application_id', $application_id)
                    ->get()
                    ->getRowArray();

    // =========================
    // Job Position Info
    // =========================
    $job = $db->table('job_vacancies')
              ->where('id', $app['job_vacancy_id'])
              ->get()
              ->getRowArray();

    // =========================
    // Applicant Profile
    // =========================
    $profileModel = new \App\Models\ApplicantModel();
    $profile = $profileModel
        ->where('id', $application_id) // Assuming profile table's PK matches application_id
        ->first();

    // =========================
    // Pass everything to the view
    // =========================
    return view('applications/edit', [
        'app'            => $app,
        'job'            => $job,
        'profile'        => $profile,
        'spouse'         => $spouse,
        'father'         => $father,
        'mother'         => $mother,
        'education_data' => $education_data,
        'applicant_work' => $applicant_work,
        'documents'      => $documents
    ]);
}

public function update($application_id = null)
{
    if (!$application_id) {
        return redirect()->to('applications');
    }

    // Make sure $db exists
    $db = \Config\Database::connect();

    // Find the application
    $jobApplication = $this->jobApplications->find($application_id);
    if (!$jobApplication) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Application not found');
    }

    // -------------------
    // Update main applicant info
    // -------------------
    $appData = [
        'job_vacancy_id' => $this->request->getPost('job_vacancy_id') ?: $jobApplication['job_vacancy_id'],
        'first_name'     => $this->request->getPost('first_name'),
        'middle_name'    => $this->request->getPost('middle_name'),
        'last_name'      => $this->request->getPost('last_name'),
        'suffix'         => $this->request->getPost('name_extension'),
        'date_of_birth'  => $this->request->getPost('birth_date'),
        'sex'            => $this->request->getPost('sex'),
        'civil_status'   => $this->request->getPost('civil_status'),
        'citizenship'    => $this->request->getPost('citizenship'),
        'email'          => $this->request->getPost('email'),
        'phone'          => $this->request->getPost('phone'),
        'updated_at'     => date('Y-m-d H:i:s')
    ];
    $this->jobApplications->update($application_id, $appData);

    // =========================
    // Update Family Background (only existing rows)
    // =========================
    $familyTable = $db->table('applicant_fam');
    $relations = ['Spouse', 'Father', 'Mother'];

    foreach ($relations as $relation) {
        $data = [];
        if ($relation === 'Spouse') {
            $data = [
                'last_name' => $this->request->getPost('spouse_surname'),
                'first_name' => $this->request->getPost('spouse_first_name'),
                'middle_name' => $this->request->getPost('spouse_middle_name'),
                'extension' => $this->request->getPost('spouse_ext_name')
            ];
        } elseif ($relation === 'Father') {
            $data = [
                'last_name' => $this->request->getPost('father_surname'),
                'first_name' => $this->request->getPost('father_first_name'),
                'middle_name' => $this->request->getPost('father_middle_name'),
                'extension' => $this->request->getPost('father_ext_name')
            ];
        } elseif ($relation === 'Mother') {
            $data = [
                'last_name' => $this->request->getPost('mother_maiden_surname'),
                'first_name' => $this->request->getPost('mother_first_name'),
                'middle_name' => $this->request->getPost('mother_middle_name')
            ];
        }

        // Update only if row exists
        $existing = $familyTable->where('application_id', $application_id)
                                ->where('relationship', $relation)
                                ->countAllResults();

        if ($existing > 0) {
            $familyTable->where('application_id', $application_id)
                        ->where('relationship', $relation)
                        ->update($data);
        }
    }
    
    // -------------------
// Update Educational Background (no duplicates)
// -------------------
$eduTable = $db->table('applicant_education');

// Fetch all existing education rows for this application
$existingEducation = $eduTable->where('application_id', $application_id)
                              ->get()
                              ->getResultArray();

// Map existing rows by level for easy update
$existingByLevel = [];
foreach ($existingEducation as $edu) {
    $existingByLevel[$edu['level']] = $edu['id'];
}

// Define form fields mapping
$educationLevels = [
    'Elementary'       => ['school' => 'elementary_school', 'degree' => 'elementary_degree', 'from' => 'elementary_from', 'to' => 'elementary_to', 'units' => 'elementary_units', 'year' => 'elementary_year', 'awards' => 'elementary_awards'],
    'Secondary'        => ['school' => 'secondary_school', 'degree' => 'secondary_degree', 'from' => 'secondary_from', 'to' => 'secondary_to', 'units' => 'secondary_units', 'year' => 'secondary_year', 'awards' => 'secondary_awards'],
    'Vocational/Trade' => ['school' => 'vocational_school', 'degree' => 'vocational_degree', 'from' => 'vocational_from', 'to' => 'vocational_to', 'units' => 'vocational_units', 'year' => 'vocational_year', 'awards' => 'vocational_awards'],
    'College'          => ['school' => 'college_school', 'degree' => 'college_degree', 'from' => 'college_from', 'to' => 'college_to', 'units' => 'college_units', 'year' => 'college_year', 'awards' => 'college_awards'],
    'Graduate Studies' => ['school' => 'graduate_school', 'degree' => 'graduate_degree', 'from' => 'graduate_from', 'to' => 'graduate_to', 'units' => 'graduate_units', 'year' => 'graduate_year', 'awards' => 'graduate_awards']
];

foreach ($educationLevels as $level => $fields) {
    $eduData = [
        'school_name'        => $this->request->getPost($fields['school']) ?: '-',
        'degree_course'      => $this->request->getPost($fields['degree']) ?: '-',
        'period_from'        => $this->request->getPost($fields['from']) ?: '-',
        'period_to'          => $this->request->getPost($fields['to']) ?: '-',
        'highest_level_units'=> $this->request->getPost($fields['units']) ?: '-',
        'year_graduated'     => $this->request->getPost($fields['year']) ?: '-',
        'awards'             => $this->request->getPost($fields['awards']) ?: '-'
    ];

    if (isset($existingByLevel[$level])) {
        // Update existing row
        $eduTable->where('id', $existingByLevel[$level])->update($eduData);
    } else {
        // Insert new row if it doesn't exist
        $eduData['application_id'] = $application_id;
        $eduData['level'] = $level;
        $eduTable->insert($eduData);
    }
}

$workTable = $db->table('applicant_work_experience');
$existingWork = $workTable->where('application_id', $application_id)
                          ->get()
                          ->getRowArray();

$workData = [
    'current_work'  => $this->request->getPost('current_work') ?: '-',
    'previous_work' => $this->request->getPost('previous_work') ?: '-',
    'duration'      => $this->request->getPost('work_duration') ?: '-',
    'awards'        => $this->request->getPost('work_awards') ?: '-'
];

if ($existingWork) {
    // Update existing work row
    $workTable->where('application_id', $application_id)
              ->update($workData);
} else {
    // Insert new row if none exists
    $workData['application_id'] = $application_id;
    $workTable->insert($workData);
}
// =========================
// Update Documents (KEEP OLD IF NO NEW UPLOAD)
// =========================
$docTable = $db->table('applicant_documents');
$existingDocs = $docTable->where('application_id', $application_id)
                         ->get()
                         ->getRowArray() ?? [];

$files = ['resume','tor','diploma','certificate'];
$uploadPath = WRITEPATH . 'uploads/';
$docData = [];

foreach ($files as $fileInput) {
    $file = $this->request->getFile($fileInput);

    if ($file && $file->isValid() && !$file->hasMoved()) {
        // New upload → replace
        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);
        $docData[$fileInput] = $newName;
    } else {
        // No upload → keep existing
        if (!empty($existingDocs[$fileInput])) {
            $docData[$fileInput] = $existingDocs[$fileInput];
        }
    }
}

// Update or insert safely
if (!empty($existingDocs)) {
    $docTable->where('application_id', $application_id)->update($docData);
} else {
    $docData['application_id'] = $application_id;
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

    // 2️⃣ Fetch the document record
    $record = $db->table('applicant_documents')
                 ->where('application_id', $application_id)
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



}
