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
        if (!$item_no) return redirect()->to('/jobs');

        $job = $this->jobPositions->where('item_no', $item_no)->first();
        if (!$job) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Job not found');

        $user_id = session()->get('user_id') ?? null;
        $profile = $this->applicantProfiles->find($user_id);

        return view('apply', ['job' => $job, 'profile' => $profile]);
    }

    public function submit($item_no = null)
{
    $job = $this->jobPositions->where('item_no', $item_no)->first();
    if (!$job) return $this->response->setStatusCode(404)->setBody('Job not found');

    $user_id = session()->get('user_id');
    if (!$user_id) return $this->response->setStatusCode(403)->setBody('User not logged in');

    $db = \Config\Database::connect();

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

    $this->jobApplications->insert([
        'user_id' => $user_id,
        'job_position_id' => $job['id'],
        'first_name' => $this->request->getPost('first_name'),
        'middle_name' => $this->request->getPost('middle_name'),
        'last_name' => $this->request->getPost('last_name'),
        'suffix' => $this->request->getPost('name_extension'),
        'date_of_birth' => $this->request->getPost('birth_date'),
        'place_of_birth' => $this->request->getPost('place_of_birth'),
        'sex' => $this->request->getPost('sex'),
        'civil_status' => $this->request->getPost('civil_status'),
        'citizenship' => $this->request->getPost('citizenship'),
        'application_status' => 'Submitted. For Evaluation',
        'applied_at' => date('Y-m-d H:i:s'),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $application_id = $this->jobApplications->getInsertID();

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
            'occupation' => null,
            'contact_number' => null
        ],
        'Mother' => [
            'last_name' => $this->request->getPost('mother_maiden_surname'),
            'first_name' => $this->request->getPost('mother_first_name'),
            'middle_name' => $this->request->getPost('mother_middle_name'),
            'extension' => null,
            'occupation' => null,
            'contact_number' => null
        ]
    ];

    foreach ($familyMembers as $relationship => $member) {
        $member['application_id'] = $application_id;
        $member['relationship'] = $relationship;
        $db->table('applicant_fam')->insert($member);
    }

$educationLevels = [
    'Elementary' => ['elementary_school','elementary_location','elementary_year','elementary_awards'],
    'High School' => ['highschool_school','highschool_location','highschool_year','highschool_awards'],
    'College' => ['college_school','college_location','college_year','college_awards'],
];

foreach ($educationLevels as $level => $fields) {

    $school = $this->request->getPost($fields[0]) ?? null;
    $location = $this->request->getPost($fields[1]) ?? null;
    $year = $this->request->getPost($fields[2]) ?? null;
    $awards = $this->request->getPost($fields[3]) ?? null;

    if ($school || $location || $year || $awards) {
        $db->table('applicant_education')->insert([
            'application_id' => $application_id,
            'level' => $level,
            'school_name' => $school,
            'location' => $location,
            'year_graduated' => $year,
            'awards' => $awards,
        ]);
    }
}

    $db->table('applicant_work_experience')->insert([
        'application_id' => $application_id,
        'current_work' => $this->request->getPost('current_work'),
        'previous_work' => $this->request->getPost('previous_work'),
        'duration' => $this->request->getPost('work_duration'),
        'awards' => $this->request->getPost('work_awards'),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);

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
        return redirect()->to('applications'); // or show error
    }

    $db = \Config\Database::connect();

    // Fetch the application
    $app = $this->jobApplications->find($application_id);
    if (!$app) return $this->response->setStatusCode(404)->setBody('Application not found');

    // Fetch related data
    $app['family'] = $db->table('applicant_fam')->where('application_id', $application_id)->get()->getResultArray();
    $app['education'] = $db->table('applicant_education')->where('application_id', $application_id)->get()->getResultArray();
    $app['work'] = $db->table('applicant_work_experience')->where('application_id', $application_id)->get()->getRowArray();
    $app['documents'] = $db->table('applicant_documents')->where('application_id', $application_id)->get()->getRowArray();
    $app['job'] = $db->table('job_positions')->where('id', $app['job_position_id'])->get()->getRowArray();

    // ✅ Fetch the user info (like Dashboard controller)
    $userModel = new \App\Models\UserModel();
    $user = $userModel->find($app['user_id']);

    // ✅ Fetch applicant profile
    $profileModel = new \App\Models\ApplicantModel();
    $profile = $profileModel->where('user_id', $app['user_id'])->first();

    // ✅ Check if profile photo exists
    $profilePhoto = null;
    if (!empty($profile['photo'])) {
        $photoPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $profile['photo'];
        if (file_exists($photoPath)) {
            $profilePhoto = $profile['photo'];
        }
    }

    // Pass all data to the view
    return view('applications/view', [
        'app' => $app,
        'user' => $user,
        'profile' => $profile,
        'profilePhoto' => $profilePhoto
    ]);
}


}
