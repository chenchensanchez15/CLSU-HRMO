<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JobApplicationModel;
use App\Models\JobPositionModel;
use App\Models\ApplicantModel;
use App\Models\ApplicantFamModel;
use App\Models\ApplicantEducationModel;
use App\Models\ApplicantWorkExperienceModel;
use App\Models\ApplicantDocumentModel; 

class Applications extends BaseController
{
    protected $jobPositions;
    protected $jobApplications;
    protected $applicantProfiles;
    protected $familyModel;
    protected $educationModel;
    protected $workModel;
    protected $documentModel; 

    public function __construct()
    {
        $this->jobPositions = new JobPositionModel();          
        $this->jobApplications = new JobApplicationModel();   
        $this->applicantProfiles = new ApplicantModel();      
        $this->familyModel = new ApplicantFamModel();         
        $this->educationModel = new ApplicantEducationModel();
        $this->workModel = new ApplicantWorkExperienceModel();
        $this->documentModel = new ApplicantDocumentModel(); 
    }

public function apply($item_no = null)
{
    if (!$item_no) {
        return redirect()->to('/jobs');
    }

    $job = $this->jobPositions
        ->where('item_no', $item_no)
        ->first();

    if (!$job) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Job not found');
    }

    $user_id = session()->get('user_id');
    if (!$user_id) {
        return redirect()->to('/login');
    }

    // ✅ CORRECT: fetch applicant profile by user_id
    $profile = $this->applicantProfiles
        ->where('user_id', $user_id)
        ->first();

    return view('apply', [
        'job'     => $job,
        'profile' => $profile
    ]);
}


public function submit($item_no = null)
{
    $job = $this->jobPositions->where('item_no', $item_no)->first();
    if (!$job) return $this->response->setStatusCode(404)->setBody('Job not found');

    $user_id = session()->get('user_id');
    if (!$user_id) return $this->response->setStatusCode(403)->setBody('User not logged in');

    $db = \Config\Database::connect();

    // Fetch applicant profile to auto-fill missing info
    $profile = $this->applicantProfiles->where('user_id', $user_id)->first();

    $files = ['resume', 'tor', 'diploma', 'certificate'];
    $uploadedFiles = [];
    $uploadPath = WRITEPATH . 'uploads/';

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

    // Insert main job application
    $this->jobApplications->insert([
        'user_id' => $user_id,
        'job_position_id' => $job['id'],
        'first_name' => $this->request->getPost('first_name') ?? $profile['first_name'],
        'middle_name' => $this->request->getPost('middle_name') ?? $profile['middle_name'],
        'last_name' => $this->request->getPost('last_name') ?? $profile['last_name'],
        'suffix' => $this->request->getPost('name_extension') ?? $profile['suffix'],
        'date_of_birth' => $this->request->getPost('birth_date') ?? $profile['date_of_birth'],
        'place_of_birth' => $this->request->getPost('place_of_birth') ?? $profile['place_of_birth'],
        'sex' => $this->request->getPost('sex') ?? $profile['sex'],
        'civil_status' => $this->request->getPost('civil_status') ?? $profile['civil_status'],
        'citizenship' => $this->request->getPost('citizenship') ?? $profile['citizenship'],
        'email' => $this->request->getPost('email') ?? $profile['email'],
        'phone' => $this->request->getPost('phone') ?? $profile['phone'],
        'height' => $this->request->getPost('height') ?? $profile['height'],
        'weight' => $this->request->getPost('weight') ?? $profile['weight'],
        'blood_type' => $this->request->getPost('blood_type') ?? $profile['blood_type'],
        'application_status' => 'Submitted. For Evaluation',
        'applied_at' => date('Y-m-d H:i:s'),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $application_id = $this->jobApplications->getInsertID();

        // Insert family members
    $familyMembers = [
        'Spouse' => [
            'last_name' => $this->request->getPost('spouse_surname'),
            'first_name' => $this->request->getPost('spouse_first_name'),
            'middle_name' => $this->request->getPost('spouse_middle_name'),
            'extension' => $this->request->getPost('spouse_ext_name'),
            'occupation' => $this->request->getPost('spouse_occupation'),
            'contact_number' => $this->request->getPost('spouse_contact')
        ],
        'Father' => [
            'last_name' => $this->request->getPost('father_surname'),
            'first_name' => $this->request->getPost('father_first_name'),
            'middle_name' => $this->request->getPost('father_middle_name'),
            'extension' => $this->request->getPost('father_ext_name'),
            'occupation' => $this->request->getPost('father_occupation') ?? '-',
            'contact_number' => $this->request->getPost('father_contact') ?? '-'
        ],
        'Mother' => [
            'last_name' => $this->request->getPost('mother_maiden_surname'),
            'first_name' => $this->request->getPost('mother_first_name'),
            'middle_name' => $this->request->getPost('mother_middle_name'),
            'extension' => null,
            'occupation' => $this->request->getPost('mother_occupation') ?? '-',
            'contact_number' => $this->request->getPost('mother_contact') ?? '-'
        ]
    ];

    foreach ($familyMembers as $relationship => $member) {
        $member['application_id'] = $application_id;
        $member['relationship'] = $relationship;
        $db->table('applicant_fam')->insert($member);
    }

        $educationLevels = [
        'Elementary'      => ['elementary_school','elementary_location','elementary_year','elementary_awards'],
        'High School'     => ['highschool_school','highschool_location','highschool_year','highschool_awards'],
        'College'         => ['college_school','college_location','college_year','college_awards'],
        'Highest Degree'  => ['highest_school','highest_location','highest_year','highest_awards'],
    ];

    foreach ($educationLevels as $level => $fields) {
        $school = $this->request->getPost($fields[0]) ?? null;
        $location = $this->request->getPost($fields[1]) ?? null;
        $year = $this->request->getPost($fields[2]) ?? null;
        $awards = $this->request->getPost($fields[3]) ?? null;

        if ($school || $location || $year || $awards) {
 
        $dbLevel = ($level === 'Highest Degree') ? $this->request->getPost('highest_degree') ?: 'Highest Degree' : $level;

        $db->table('applicant_education')->insert([
            'application_id' => $application_id,
            'level' => $dbLevel,
            'school_name' => $school,
            'location' => $location,
            'year_graduated' => $year,
            'awards' => $awards,
        ]);

        }
    }

    // Insert work experience
    $db->table('applicant_work_experience')->insert([
        'application_id' => $application_id,
        'current_work' => $this->request->getPost('current_work'),
        'previous_work' => $this->request->getPost('previous_work'),
        'duration' => $this->request->getPost('work_duration'),
        'awards' => $this->request->getPost('work_awards'),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);

    // Insert documents
    $db->table('applicant_documents')->insert([
        'application_id' => $application_id,
        'resume' => $uploadedFiles['resume'],
        'tor' => $uploadedFiles['tor'],
        'diploma' => $uploadedFiles['diploma'],
        'certificate' => $uploadedFiles['certificate'],
        'uploaded_at' => date('Y-m-d H:i:s'),
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

    // Fetch the application
    $app = $this->jobApplications->find($application_id);
    if (!$app) {
        return $this->response->setStatusCode(404)->setBody('Application not found');
    }

    // Fetch related applicant data
    $app['family']    = $db->table('applicant_fam')->where('application_id', $application_id)->get()->getResultArray();
    $app['education'] = $db->table('applicant_education')->where('application_id', $application_id)->get()->getResultArray();
    $app['work']      = $db->table('applicant_work_experience')->where('application_id', $application_id)->get()->getRowArray();
    $app['documents'] = $db->table('applicant_documents')->where('application_id', $application_id)->get()->getRowArray();

    // ✅ Fetch job position details correctly
    $jobPosition = $db->table('job_positions')->where('id', $app['job_position_id'])->get()->getRowArray();
    $app['job'] = $jobPosition; // now $app['job'] contains the full job info

    // Fetch user info
    $userModel = new \App\Models\UserModel();
    $user = $userModel->find($app['user_id']);

    // Fetch applicant profile
    $profileModel = new \App\Models\ApplicantModel();
    $profile = $profileModel->where('user_id', $app['user_id'])->first();

    // Check profile photo
    $profilePhoto = null;
    if (!empty($profile['photo'])) {
        $photoPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $profile['photo'];
        if (file_exists($photoPath)) {
            $profilePhoto = $profile['photo'];
        }
    }

    // Pass all data to the view
    return view('applications/view', [
        'app'          => $app,
        'user'         => $user,
        'profile'      => $profile,
        'profilePhoto' => $profilePhoto
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
    $family = $db->table('applicant_fam')
        ->where('application_id', $application_id)
        ->get()
        ->getResultArray();

    // Separate family by relationship for easier prefill
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
$education = $db->table('applicant_education')
    ->where('application_id', $application_id)
    ->get()
    ->getResultArray();

// Separate education by level
$elementary = $highschool = $college = $highest = [];

// Loop through all education rows
foreach ($education as $edu) {
    if ($edu['level'] === 'Elementary') {
        $elementary = $edu;
    } elseif ($edu['level'] === 'High School') {
        $highschool = $edu;
    } elseif ($edu['level'] === 'College') {
        $college = $edu;
    } else {
        // Anything that is not Elementary, High School, College → treat as Highest Degree
        $highest = $edu;
    }
}

    // =========================
    // Work Experience (SINGLE ROW)
    // =========================
    $applicant_work = $db->table('applicant_work_experience')
        ->where('application_id', $application_id)
        ->get()
        ->getRowArray();

    // =========================
    // Documents
    // =========================
    $documents = $db->table('applicant_documents')
        ->where('application_id', $application_id)
        ->get()
        ->getRowArray();

    // =========================
    // Job Position Info
    // =========================
    $job = $db->table('job_positions')
        ->where('id', $app['job_position_id'])
        ->get()
        ->getRowArray();

    // =========================
    // Applicant Profile
    // =========================
    $profileModel = new \App\Models\ApplicantModel();
    $profile = $profileModel
        ->where('user_id', $app['user_id'])
        ->first();

    // =========================
    // Pass EVERYTHING to view
    // =========================
    return view('applications/edit', [
        'app'             => $app,
        'job'             => $job,
        'profile'         => $profile,
        'spouse'          => $spouse,
        'father'          => $father,
        'mother'          => $mother,
        'elementary'      => $elementary,
        'highschool'      => $highschool,
        'college'         => $college,
        'highest'         => $highest,
        'applicant_work'  => $applicant_work,
        'documents'       => $documents
    ]);
}

public function update($application_id = null)
{
    if (!$application_id) {
        return redirect()->to('applications');
    }

    $db = \Config\Database::connect();

    $jobApplication = $this->jobApplications->find($application_id);
    if (!$jobApplication) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Application not found');
    }

    // -------------------
    // Update Main Applicant Info
    // -------------------
    $appData = [
        'job_position_id' => $this->request->getPost('job_position_id'),
        'first_name'      => $this->request->getPost('first_name'),
        'middle_name'     => $this->request->getPost('middle_name'),
        'last_name'       => $this->request->getPost('last_name'),
        'suffix'          => $this->request->getPost('name_extension'),
        'date_of_birth'   => $this->request->getPost('birth_date'),
        'place_of_birth'  => $this->request->getPost('place_of_birth'),
        'sex'             => $this->request->getPost('sex'),
        'civil_status'    => $this->request->getPost('civil_status'),
        'citizenship'     => $this->request->getPost('citizenship'),
        'email'           => $this->request->getPost('email'),
        'phone'           => $this->request->getPost('phone'),
        'height'          => $this->request->getPost('height'),
        'weight'          => $this->request->getPost('weight'),
        'blood_type'      => $this->request->getPost('blood_type'),
        'updated_at'      => date('Y-m-d H:i:s')
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

// Map existing rows by level for Elementary, High School, College
$existingByLevel = [];
$highestId = null;

foreach ($existingEducation as $edu) {
    if (in_array($edu['level'], ['Elementary', 'High School', 'College'])) {
        $existingByLevel[$edu['level']] = $edu['id'];
    } else {
        // Any other level → treat as Highest Degree
        $highestId = $edu['id'];
    }
}

// Define form fields
$levels = [
    'Elementary'      => ['elementary_school','elementary_location','elementary_year','elementary_awards'],
    'High School'     => ['highschool_school','highschool_location','highschool_year','highschool_awards'],
    'College'         => ['college_school','college_location','college_year','college_awards'],
    'Highest Degree'  => ['highest_school','highest_location','highest_year','highest_awards']
];

foreach ($levels as $originalLevel => $fields) {
    $school   = $this->request->getPost($fields[0]) ?: '-';
    $location = $this->request->getPost($fields[1]) ?: '-';
    $year     = $this->request->getPost($fields[2]) ?: '-';
    $awards   = $this->request->getPost($fields[3]) ?: '-';

    // Rename Highest Degree if user typed a custom name
    $dbLevel = ($originalLevel === 'Highest Degree') ? ($this->request->getPost('highest_degree') ?: 'Highest Degree') : $originalLevel;

    if ($originalLevel === 'Highest Degree') {
        if ($highestId) {
            // Update existing Highest Degree row
            $eduTable->where('id', $highestId)->update([
                'level'          => $dbLevel,
                'school_name'    => $school,
                'location'       => $location,
                'year_graduated' => $year,
                'awards'         => $awards
            ]);
        } else {
            // Insert new Highest Degree row if none exists
            $eduTable->insert([
                'application_id' => $application_id,
                'level'          => $dbLevel,
                'school_name'    => $school,
                'location'       => $location,
                'year_graduated' => $year,
                'awards'         => $awards
            ]);
        }
    } else {
        // Elementary, High School, College
        if (isset($existingByLevel[$originalLevel])) {
            // Update existing row
            $eduTable->where('id', $existingByLevel[$originalLevel])->update([
                'school_name'    => $school,
                'location'       => $location,
                'year_graduated' => $year,
                'awards'         => $awards
            ]);
        } else {
            // Insert new row if it doesn't exist
            $eduTable->insert([
                'application_id' => $application_id,
                'level'          => $dbLevel,
                'school_name'    => $school,
                'location'       => $location,
                'year_graduated' => $year,
                'awards'         => $awards
            ]);
        }
    }
}


    // =========================
    // Update Work Experience (existing only)
    // =========================
    $workTable = $db->table('applicant_work_experience');
    $workData = [
        'current_work' => $this->request->getPost('current_work'),
        'previous_work' => $this->request->getPost('previous_work'),
        'duration' => $this->request->getPost('work_duration'),
        'awards' => $this->request->getPost('work_awards')
    ];

    $existing = $workTable->where('application_id', $application_id)->countAllResults();
    if ($existing > 0) {
        $workTable->where('application_id', $application_id)->update($workData);
    }


    // =========================
    // Redirect to dashboard with success
    // =========================
     $files = ['resume','tor','diploma','certificates'];
    $uploadPath = WRITEPATH . 'uploads/';
    $docTable = $db->table('applicant_documents');
    $docData = [];

    foreach ($files as $fileInput) {
        $file = $this->request->getFile($fileInput);
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $docData[$fileInput] = $newName;
        }
    }

    if (!empty($docData)) {
        $docTable->where('application_id', $application_id)->update($docData);
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
    $profile = $db->table('applicant_profiles')
                  ->select('resume, user_id')
                  ->where('id', $profile_id)
                  ->get()
                  ->getRowArray();

    if (!$profile || empty($profile['resume'])) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Resume not uploaded');
    }

    // 🔒 Check if the current user is allowed
    $current_user_id = session()->get('user_id');
    if (!$current_user_id || $current_user_id != $profile['user_id']) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Unauthorized access');
    }

    $file = $profile['resume'];
    $filePath = FCPATH . 'uploads/' . $file;

    if (!file_exists($filePath)) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Resume file not found on server');
    }

    // Stream resume inline
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
    $profile = $db->table('applicant_profiles')
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
