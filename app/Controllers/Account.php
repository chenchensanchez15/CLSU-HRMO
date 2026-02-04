<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ApplicantModel;
use App\Models\JobApplicationModel;
use App\Models\ApplicantDocumentsModel;
use App\Models\ApplicantCivilServiceModel;
use App\Models\ApplicantTrainingModel;
use App\Models\ApplicantFamModel;


class Account extends BaseController
{
public function personal()
{
    $session = session();
    $userId = $session->get('user_id');

    // Models
    $userModel           = new \App\Models\UserModel();
    $applicantModel      = new \App\Models\ApplicantModel();
    $educationModel      = new \App\Models\ApplicantEducationModel();
    $workModel           = new \App\Models\ApplicantWorkExperienceModel();
    $civilModel          = new \App\Models\ApplicantCivilServiceModel();
    $trainingModel       = new \App\Models\ApplicantTrainingModel();
    $trainingCategoryModel = new \App\Models\TrainingCategoryModel();
    $fileModel           = new \App\Models\ApplicantDocumentsModel();
    $familyModel         = new \App\Models\ApplicantFamModel();

    // ----------------- USER & PROFILE -----------------
    $user    = $userModel->find($userId);
    $profile = $applicantModel->where('user_id', $userId)->first();

// ----------------- FAMILY BACKGROUND -----------------
$familyRecords = $familyModel->where('user_id', $userId)->findAll();

// Default structure (empty strings if no data)
$familyProfile = [
    'spouse' => ['first_name'=>'', 'middle_name'=>'', 'last_name'=>'', 'extension'=>'', 'occupation'=>'', 'contact_no'=>''],
    'father' => ['first_name'=>'', 'middle_name'=>'', 'last_name'=>'', 'extension'=>'', 'occupation'=>'', 'contact_no'=>''],
    'mother' => ['first_name'=>'', 'middle_name'=>'', 'last_name'=>'', 'occupation'=>'', 'contact_no'=>''],
];

// Overwrite defaults with actual data if exists
foreach ($familyRecords as $member) {
    $relationship = strtolower($member['relationship']); // spouse, father, mother

    if (!isset($familyProfile[$relationship])) continue;

    $familyProfile[$relationship] = [
        'first_name'  => $member['first_name'] ?? '',
        'middle_name' => $member['middle_name'] ?? '',
        'last_name'   => $member['last_name'] ?? '',
        'extension'   => $member['extension'] ?? '',
        'occupation'  => $member['occupation'] ?? '',
        'contact_no'  => $member['contact_no'] ?? '',
    ];
}
    // ----------------- EDUCATION -----------------
    $educationRecords = $educationModel->where('user_id', $userId)->findAll();
    $levels = ['Elementary','Secondary','Vocational/Trade','College','Graduate Studies'];
    $finalEducation = [];

    foreach ($levels as $level) {
        $edu = null;

        foreach ($educationRecords as $record) {
            if ($record['level'] === $level) {
                $edu = $record;
                break;
            }
        }

        if (!$edu) {
            $edu = [
                'id' => null,
                'user_id' => $userId,
                'level' => $level,
                'school_name' => '-',
                'degree_course' => '-',
                'period_from' => '-',
                'period_to' => '-',
                'highest_level_units' => '-',
                'year_graduated' => '-',
                'awards' => '-',
            ];
        }

        $finalEducation[] = $edu;
    }

    // ----------------- WORK EXPERIENCE -----------------
    $workRecords = $workModel
        ->where('user_id', $userId)
        ->orderBy('date_from', 'DESC')
        ->findAll();

    foreach ($workRecords as &$work) {
        $work['govt_service'] = in_array($work['govt_service'], ['Yes','No'], true)
            ? $work['govt_service']
            : '-';
    }
    unset($work);

    // ----------------- CIVIL SERVICE -----------------
    $civilRecords = $civilModel
        ->where('user_id', $userId)
        ->orderBy('date_of_exam','DESC')
        ->findAll();

    foreach ($civilRecords as &$civil) {
        $civil['date_of_exam'] = (!empty($civil['date_of_exam']) && $civil['date_of_exam'] !== '0000-00-00')
            ? date('d/m/Y', strtotime($civil['date_of_exam']))
            : '-';

        $civil['license_valid_until'] = (!empty($civil['license_valid_until']) && $civil['license_valid_until'] !== '0000-00-00')
            ? date('d/m/Y', strtotime($civil['license_valid_until']))
            : '-';

        $civil['eligibility'] = $civil['eligibility'] ?? '-';
        $civil['rating'] = $civil['rating'] ?? '-';
        $civil['place_of_exam'] = $civil['place_of_exam'] ?? '-';
        $civil['license_no'] = $civil['license_no'] ?? '-';
    }
    unset($civil);

    // ----------------- TRAININGS -----------------
    $trainingCategories = $trainingCategoryModel->findAll();

    $trainingRecords = $trainingModel
        ->select('applicant_trainings.*, lib_training_category.training_category_name')
        ->join(
            'lib_training_category',
            'lib_training_category.id_training_category = applicant_trainings.training_category_id',
            'left'
        )
        ->where('user_id', $userId)
        ->orderBy('added_date','DESC')
        ->findAll();

    // ----------------- FILES -----------------
    $fileRecords = $fileModel->where('user_id', $userId)->first() ?? [
        'resume' => '',
        'tor' => '',
        'diploma' => '',
        'certificate' => '',
        'uploaded_at' => '',
    ];

    // ----------------- RETURN VIEW -----------------
    return view('account/personal', [
        'user'              => $user,
        'profile'           => $profile,
        'familyProfile'     => $familyProfile,
        'educationRecords'  => $finalEducation,
        'workRecords'       => $workRecords,
        'civilRecords'      => $civilRecords,
        'trainingRecords'   => $trainingRecords,
        'trainingCategories'=> $trainingCategories,
        'fileRecords'       => $fileRecords,
    ]);
}

public function update()
{
    $session = session();
    $userId = $session->get('user_id');

    $userModel = new \App\Models\UserModel();
    $applicantModel = new \App\Models\ApplicantModel();
    $applicantFamModel = new \App\Models\ApplicantFamModel();
    $jobApplicationModel = new \App\Models\JobApplicationModel();

    // --- Update user table ---
    $userData = [
        'first_name'  => $this->request->getPost('first_name') ?? '',
        'middle_name' => $this->request->getPost('middle_name') ?? '',
        'last_name'   => $this->request->getPost('last_name') ?? '',
        'extension'   => $this->request->getPost('suffix') ?? '',
        'email'       => $this->request->getPost('email') ?? ''
    ];
    $userModel->update($userId, $userData);

    // --- Update applicant personal info ---
    $profileData = [
        'first_name'          => $this->request->getPost('first_name') ?? '',
        'middle_name'         => $this->request->getPost('middle_name') ?? '',
        'last_name'           => $this->request->getPost('last_name') ?? '',
        'suffix'              => $this->request->getPost('suffix') ?? '',
        'sex'                 => $this->request->getPost('sex') ?? '',
        'date_of_birth'       => $this->request->getPost('date_of_birth') ?? '',
        'civil_status'        => $this->request->getPost('civil_status') ?? '',
        'email'               => $this->request->getPost('email') ?? '',
        'phone'               => $this->request->getPost('phone') ?? '',
        'citizenship'         => $this->request->getPost('citizenship') ?? '',
        'residential_address' => $this->request->getPost('residential_address') ?? '',
        'permanent_address'   => $this->request->getPost('permanent_address') ?? '',
    ];

    $profile = $applicantModel->where('user_id', $userId)->first();
    if ($profile) {
        $applicantModel->update($profile['id'], $profileData);
    } else {
        $profileData['user_id'] = $userId;
        $applicantModel->insert($profileData);
        $profile['id'] = $applicantModel->getInsertID();
    }

    // --- Update applicant family info with validation ---
    $familyMembers = ['Spouse', 'Father', 'Mother'];

    foreach ($familyMembers as $relation) {

        $firstName  = $this->request->getPost(strtolower($relation).'_first_name') ?? '';
        $middleName = $this->request->getPost(strtolower($relation).'_middle_name') ?? '';
        $lastName   = $this->request->getPost(strtolower($relation).'_last_name') ?? '';
        $extension  = $this->request->getPost(strtolower($relation).'_extension') ?? '';
        $occupation = $this->request->getPost(strtolower($relation).'_occupation') ?? '';
        $contactNo  = $this->request->getPost(strtolower($relation).'_contact_no') ?? '';

        // --- Validation ---
        if (!empty($contactNo) && !preg_match('/^\d{11}$/', $contactNo)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "$relation Contact No. must be exactly 11 digits."
            ]);
        }

        // --- Prepare data ---
        $famData = [
            'user_id'     => $userId,
            'relationship'=> $relation,
            'first_name'  => $firstName,
            'middle_name' => $middleName,
            'last_name'   => $lastName,
            'extension'   => $extension,
            'occupation'  => $occupation,
            'contact_no'  => $contactNo
        ];

        // --- Insert or Update ---
        $existing = $applicantFamModel
            ->where('user_id', $userId)
            ->where('relationship', $relation)
            ->first();

        if ($existing) {
            $applicantFamModel->update($existing['id'], $famData);
        } else {
            $applicantFamModel->insert($famData);
        }
    }

    // --- Update submitted job applications ---
    $submittedApps = $jobApplicationModel
        ->where('user_id', $userId)
        ->where('application_status', 'Submitted. For Evaluation')
        ->findAll();

    foreach ($submittedApps as $app) {
        $jobApplicationModel->update($app['id_job_application'], [
            'first_name'          => $profileData['first_name'],
            'middle_name'         => $profileData['middle_name'],
            'last_name'           => $profileData['last_name'],
            'suffix'              => $profileData['suffix'],
            'sex'                 => $profileData['sex'],
            'date_of_birth'       => $profileData['date_of_birth'],
            'civil_status'        => $profileData['civil_status'],
            'email'               => $profileData['email'],
            'phone'               => $profileData['phone'],
            'citizenship'         => $profileData['citizenship'],
            'residential_address' => $profileData['residential_address'],
            'permanent_address'   => $profileData['permanent_address'],
        ]);
    }

    return $this->response->setJSON([
        'success' => true,
        'message' => 'Profile and Family Background updated successfully!'
    ]);
}


public function updatePhoto()
{
    $session = session();
    $userId = $session->get('user_id');

    $applicantModel = new ApplicantModel();
    $photoFile = $this->request->getFile('photo');

    if (!$photoFile || !$photoFile->isValid()) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'No valid photo uploaded.'
        ]);
    }

    $photoName = $photoFile->getRandomName();
    $photoFile->move(FCPATH . 'uploads', $photoName);

    // Update only the photo column
    $profile = $applicantModel->where('user_id', $userId)->first();
    if ($profile) {
        $applicantModel->update($profile['id'], ['photo' => $photoName]);
    } else {
        $applicantModel->insert([
            'user_id' => $userId,
            'photo' => $photoName
        ]);
    }

    return $this->response->setJSON([
        'success' => true,
        'message' => 'Profile photo updated successfully!',
        'photo'   => $photoName
    ]);
}

    public function changePassword()
    {
        $session = session();
        $userId = $session->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        return view('account/change_password', [
            'user' => $user
        ]);
    }

    public function updatePassword()
    {
        $session = session();
        $userId = $session->get('user_id');

        $current = $this->request->getPost('current_password');
        $new = $this->request->getPost('new_password');
        $confirm = $this->request->getPost('confirm_password');

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!password_verify($current, $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        if ($new !== $confirm) {
            return redirect()->back()->with('error', 'New password and confirm password do not match.');
        }

        $userModel->update($userId, [
            'password' => password_hash($new, PASSWORD_DEFAULT),
            'first_login' => 0
        ]);

        $session->set('first_login', 0);

        return redirect()->to('/dashboard')->with('success', 'Password updated successfully!');
    }

public function updateEducation()
{
    if(!$this->request->isAJAX()) {
        return redirect()->to('account/personal');
    }

    $education = $this->request->getPost('education');

    if(empty($education)) {
        return $this->response->setJSON(['success' => false, 'message' => 'No data received.']);
    }

    $userId = session()->get('user_id');
    $educationModel = new \App\Models\ApplicantEducationModel();

    $levels = ['Elementary','Secondary','Vocational/Trade','College','Graduate Studies'];

    foreach($levels as $index => $level){
        $eduData = $education[$index] ?? [];

        $data = [
            'user_id' => $userId,
            'school_name' => isset($eduData['school_name']) ? $eduData['school_name'] : null,
            'degree_course' => isset($eduData['degree_course']) ? $eduData['degree_course'] : null,
            'period_from' => isset($eduData['period_from']) ? $eduData['period_from'] : null,
            'period_to' => isset($eduData['period_to']) ? $eduData['period_to'] : null,
            'highest_level_units' => isset($eduData['highest_level_units']) ? $eduData['highest_level_units'] : null,
            'year_graduated' => isset($eduData['year_graduated']) ? $eduData['year_graduated'] : null,
            'awards' => isset($eduData['awards']) ? $eduData['awards'] : null
        ];

        $existing = $educationModel->where('user_id', $userId)
                                   ->where('level', $level)
                                   ->first();

        if($existing){
            $educationModel->update($existing['id'], $data);
        } else {
            $data['level'] = $level;
            $educationModel->insert($data);
        }
    }

    return $this->response->setJSON([
        'success' => true,
        'message' => 'Educational Background updated successfully!'
    ]);
}

public function updateWorkExperience()
{
    if (!$this->request->isAJAX()) {
        return redirect()->to('account/personal');
    }

    // Get the single work entry from the modal form
    $id = $this->request->getPost('id');
    $userId = session()->get('user_id');

    $data = [
        'user_id' => $userId,
        'position_title' => $this->request->getPost('position_title') ?: null,
        'office' => $this->request->getPost('office') ?: null,
        'date_from' => $this->request->getPost('date_from') ? date('Y-m-d', strtotime($this->request->getPost('date_from'))) : null,
        'date_to' => $this->request->getPost('date_to') ? date('Y-m-d', strtotime($this->request->getPost('date_to'))) : null,
        'status_of_appointment' => $this->request->getPost('status_of_appointment') ?: null,
        'govt_service' => $this->request->getPost('govt_service') ?: null,
    ];

    $workModel = new \App\Models\ApplicantWorkExperienceModel();

    try {
        if ($id) {
            // Update existing record
            $existing = $workModel->where('id', $id)->where('user_id', $userId)->first();
            if ($existing) {
                $workModel->update($existing['id'], $data);
                $message = 'Work experience updated successfully!';
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Record not found.'
                ]);
            }
        } else {
            // Insert new record
            $workModel->insert($data);
            $message = 'Work experience added successfully!';
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $message
        ]);

    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}
public function deleteWorkExperience($id = null)
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request.'
        ]);
    }

    if (!$id) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Missing ID.'
        ]);
    }

    $model = new \App\Models\ApplicantWorkExperienceModel();

    if ($model->delete($id)) {
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Work experience deleted successfully.'
        ]);
    }

    return $this->response->setJSON([
        'success' => false,
        'message' => 'Unable to delete record.'
    ]);
}
public function updateCivilService()
{
    if (!$this->request->isAJAX()) {
        return redirect()->to('account/personal');
    }

    $userId = session()->get('user_id');
    $civilModel = new \App\Models\ApplicantCivilServiceModel();

    $id = $this->request->getPost('id'); // null if adding new
    $data = [
        'user_id' => $userId,
        'eligibility' => $this->request->getPost('eligibility') ?: null,
        'rating' => $this->request->getPost('rating') ?: null,
        'date_of_exam' => $this->request->getPost('date_of_exam') ?: null,
        'place_of_exam' => $this->request->getPost('place_of_exam') ?: null,
        'license_no' => $this->request->getPost('license_no') ?: null,
        'license_valid_until' => $this->request->getPost('license_valid_until') ?: null,
    ];

    if ($id) {
        $existing = $civilModel->where('id', $id)->where('user_id', $userId)->first();
        if ($existing) {
            $civilModel->update($existing['id'], $data);
            $message = 'Civil Service record updated successfully!';
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Record not found.'
            ]);
        }
    } else {
        $civilModel->insert($data);
        $message = 'Civil Service record added successfully!';
    }

    return $this->response->setJSON([
        'success' => true,
        'message' => $message
    ]);
}

public function deleteCivilService($id = null)
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request.'
        ]);
    }

    if (!$id) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Missing ID.'
        ]);
    }

    $civilModel = new \App\Models\ApplicantCivilServiceModel();

    if ($civilModel->delete($id)) {
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Civil Service record deleted successfully.'
        ]);
    }

    return $this->response->setJSON([
        'success' => false,
        'message' => 'Unable to delete record.'
    ]);
}

public function updateFiles()
{
    $session = session();
    $userId = $session->get('user_id');

    $fileModel = new \App\Models\ApplicantDocumentsModel();

    // Check if user already has a record
    $uploaded = $fileModel->where('user_id', $userId)->first();

    $data = [];

    helper('filesystem');

    // Handle file uploads
    foreach(['resume','tor','diploma','certificate'] as $field){
        $file = $this->request->getFile($field);

        if($file && $file->isValid() && !$file->hasMoved()){
            $newName = $file->getRandomName();
            $file->move(FCPATH.'uploads', $newName);
            $data[$field] = $newName;
        } elseif ($uploaded && isset($uploaded[$field])) {
            // Keep old file if no new upload
            $data[$field] = $uploaded[$field];
        }
    }

    $data['uploaded_at'] = date('Y-m-d H:i:s');

    if($uploaded){
        $fileModel->update($uploaded['id'], $data);
    } else {
        $data['user_id'] = $userId;
        $fileModel->insert($data);
    }

    return $this->response->setJSON([
        'status' => 'success',
        'message' => 'Documents updated successfully!'
    ]);
}

  public function trainings()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('applicant_trainings');
        $builder->select('applicant_trainings.*, lib_training_category.training_category_name');
        $builder->join(
            'lib_training_category',
            'lib_training_category.id_training_category = applicant_trainings.training_category_id',
            'left'
        );
        $builder->where('applicant_trainings.user_id', session()->get('user_id'));
        $trainingRecords = $builder->get()->getResultArray();

        $categoryBuilder = $db->table('lib_training_category');
        $trainingCategories = $categoryBuilder->get()->getResultArray();

        return view('account/trainings', compact('trainingRecords', 'trainingCategories'));
    }

    public function addApplicantTraining()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success'=>false,'message'=>'Invalid request.']);
        }

        $userId = session()->get('user_id');
        $fileName = null;

        $file = $this->request->getFile('training_certificate_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $file->move(FCPATH . 'uploads', $fileName);
        }

       $data = [
    'user_id' => $userId,
    'training_name' => $this->request->getPost('training_name'),
    'training_category_id' => $this->request->getPost('training_category_id'),
    'date_from' => $this->request->getPost('date_from'),       // fix here
    'date_to' => $this->request->getPost('date_to'),           // fix here
    'training_facilitator' => $this->request->getPost('training_facilitator'),
    'training_hours' => $this->request->getPost('training_hours'),
    'training_sponsor' => $this->request->getPost('training_sponsor'),
    'training_remarks' => $this->request->getPost('training_remarks'),
    'certificate_file' => $fileName,
    'added_date' => date('Y-m-d H:i:s')
];


        $trainingModel = new ApplicantTrainingModel();

        if (!$trainingModel->insert($data)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Insert failed',
                'errors' => $trainingModel->errors()
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Training saved successfully!'
        ]);
    }

public function updateTraining()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON(['success'=>false,'message'=>'Invalid request.']);
    }

    $id = $this->request->getPost('id');
    $userId = session()->get('user_id');

    $trainingModel = new ApplicantTrainingModel();
    $training = $trainingModel->find($id);

    if (!$training) {
        return $this->response->setJSON(['success'=>false,'message'=>'Training not found.']);
    }

    // Handle file upload
    $fileName = $training['certificate_file'];
    $file = $this->request->getFile('training_certificate_file');
    if ($file && $file->isValid() && !$file->hasMoved()) {
        $fileName = $file->getRandomName();
        $file->move(FCPATH . 'uploads', $fileName);
    }

    // Update data
    $data = [
        'training_name' => $this->request->getPost('training_name'),
        'training_category_id' => $this->request->getPost('training_category_id'),
        'date_from' => $this->request->getPost('date_from'), // <-- fixed
        'date_to' => $this->request->getPost('date_to'),     // <-- fixed
        'training_facilitator' => $this->request->getPost('training_facilitator'),
        'training_hours' => $this->request->getPost('training_hours'),
        'training_sponsor' => $this->request->getPost('training_sponsor'),
        'training_remarks' => $this->request->getPost('training_remarks'),
        'certificate_file' => $fileName,
    ];

    $trainingModel->update($id, $data);

    return $this->response->setJSON([
        'success' => true,
        'message' => 'Training updated successfully!'
    ]);
}
    public function deleteTraining($id)
    {
        $trainingModel = new ApplicantTrainingModel();
        if ($trainingModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Training deleted successfully!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete training.'
            ]);
        }
    }
}
