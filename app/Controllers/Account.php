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
    $userModel             = new \App\Models\UserModel();
    $applicantModel        = new \App\Models\ApplicantModel();
    $educationModel        = new \App\Models\ApplicantEducationModel();
    $workModel             = new \App\Models\ApplicantWorkExperienceModel();
    $civilModel            = new \App\Models\ApplicantCivilServiceModel();
    $trainingModel         = new \App\Models\ApplicantTrainingModel();
    $trainingCategoryModel = new \App\Models\TrainingCategoryModel();
    $fileModel             = new \App\Models\ApplicantDocumentsModel();
    $familyModel           = new \App\Models\ApplicantFamModel();

    // Load degree tables
    $degreeModel       = new \App\Models\DegreeModel();
    $degreeLevelModel  = new \App\Models\DegreeLevelModel();
    $libDegrees        = $degreeModel->findAll();
    $libDegreeLevels   = $degreeLevelModel->findAll();

    // ----------------- USER & PROFILE -----------------
    $user    = $userModel->find($userId);
    $profile = $applicantModel->where('user_id', $userId)->first();

    if (!$profile) {
        $profile = [
            'first_name' => '',
            'middle_name' => '',
            'last_name' => '',
            'suffix' => '',
            'sex' => '',
            'date_of_birth' => '',
            'civil_status' => '',
            'email' => '',
            'phone' => '',
            'citizenship' => '',
            'residential_address' => '',
            'permanent_address' => ''
        ];
    }

    // ----------------- FAMILY -----------------
    $familyRecords = $familyModel->where('user_id', $userId)->findAll();
    $familyProfile = [
        'spouse' => ['first_name'=>'', 'middle_name'=>'', 'last_name'=>'', 'extension'=>'', 'occupation'=>'', 'contact_no'=>''],
        'father' => ['first_name'=>'', 'middle_name'=>'', 'last_name'=>'', 'extension'=>'', 'occupation'=>'', 'contact_no'=>''],
        'mother' => ['first_name'=>'', 'middle_name'=>'', 'last_name'=>'', 'occupation'=>'', 'contact_no'=>''],
    ];
    foreach ($familyRecords as $member) {
        $relationship = strtolower($member['relationship']);
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
    $finalEducation = [];

    foreach($libDegreeLevels as $levelObj) {
        $levelId = $levelObj['id_degree_level'];

        $levelRecords = array_filter($educationRecords, fn($r) => $r['degree_level_id'] == $levelId);

        if(empty($levelRecords)){
            $levelRecords[] = [
                'id' => null,
                'user_id' => $userId,
                'degree_level_id' => $levelId,
                'degree_id' => null,
                'school_name' => '-',
                'degree_course' => '-',
                'period_from' => '-',
                'period_to' => '-',
                'highest_level_units' => '-',
                'year_graduated' => '-',
                'awards' => '-',
            ];
        }

        foreach($levelRecords as $edu){
            $finalEducation[] = $edu;
        }
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
            ? date('F j, Y', strtotime($civil['date_of_exam']))
            : 'N/A';

        $civil['license_valid_until'] =
            (!empty($civil['license_valid_until'])
            && $civil['license_valid_until'] !== '0000-00-00'
            && strtotime($civil['license_valid_until']) !== false)
                ? date('F j, Y', strtotime($civil['license_valid_until']))
                : 'N/A';

        $civil['eligibility'] = $civil['eligibility'] ?: 'N/A';
        $civil['rating'] = $civil['rating'] ?: 'N/A';
        $civil['place_of_exam'] = $civil['place_of_exam'] ?: 'N/A';
        $civil['license_no'] = $civil['license_no'] ?: 'N/A';
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

    $totalDurationSeconds = 0; // To accumulate all training durations

    foreach ($trainingRecords as &$training) {
        $training['training_name']          = $training['training_name'] ?: 'N/A';
        $training['training_category_name'] = $training['training_category_name'] ?: 'N/A';
        $training['training_venue']         = $training['training_venue'] ?: 'N/A';
        $training['training_facilitator']   = $training['training_facilitator'] ?: 'N/A';
        $training['training_hours']         = $training['training_hours'] ?: 'N/A';
        $training['training_sponsor']       = $training['training_sponsor'] ?: 'N/A';
        $training['training_remarks']       = $training['training_remarks'] ?: 'N/A';
        $training['certificate_file']       = $training['certificate_file'] ?: '';

        // Format dates
        $training['date_from_formatted'] = (!empty($training['date_from']) && $training['date_from'] !== '0000-00-00')
            ? date('F j, Y', strtotime($training['date_from']))
            : 'N/A';
        $training['date_to_formatted'] = (!empty($training['date_to']) && $training['date_to'] !== '0000-00-00')
            ? date('F j, Y', strtotime($training['date_to']))
            : 'N/A';

        // ----------------- Calculate Training Duration -----------------
        if(!empty($training['date_from']) && !empty($training['date_to']) && $training['date_from'] !== '0000-00-00' && $training['date_to'] !== '0000-00-00') {
            $from = new \DateTime($training['date_from']);
            $to   = new \DateTime($training['date_to']);
            $diff = $from->diff($to);

            $training['training_duration'] = 
                ($diff->y > 0 ? $diff->y . ' yr ' : '') .
                ($diff->m > 0 ? $diff->m . ' mo ' : '') .
                ($diff->d > 0 ? $diff->d . ' days' : '');

            // Add to total duration (in seconds)
            $totalDurationSeconds += ($to->getTimestamp() - $from->getTimestamp());
        } else {
            $training['training_duration'] = '-';
        }
    }
    unset($training);

    // Convert total duration in seconds to years, months, days
    $totalDuration = '';
    if($totalDurationSeconds > 0) {
        $base = new \DateTime('@0');
        $end  = new \DateTime('@' . $totalDurationSeconds);
        $diff = $base->diff($end);
        $totalDuration = 
            ($diff->y > 0 ? $diff->y . ' yr ' : '') .
            ($diff->m > 0 ? $diff->m . ' mo ' : '') .
            ($diff->d > 0 ? $diff->d . ' days' : '');
    }
// ----------------- FILES -----------------
$fileRecords = $fileModel->where('user_id', $userId)->first() ?? [
    'pds'                => '',
    'performance_rating' => '',
    'resume'             => '',
    'tor'                => '',
    'diploma'            => '',
    'uploaded_at'        => '',
];

    return view('account/personal', [
        'user'               => $user,
        'profile'            => $profile,
        'familyProfile'      => $familyProfile,
        'educationRecords'   => $finalEducation,
        'workRecords'        => $workRecords,
        'civilRecords'       => $civilRecords,
        'trainingRecords'    => $trainingRecords,
        'trainingCategories' => $trainingCategories,
        'totalTrainingDuration' => $totalDuration, // pass total duration to view
        'fileRecords'        => $fileRecords,
        'libDegrees'         => $libDegrees,
        'libDegreeLevels'    => $libDegreeLevels,
    ]);
}

public function update()
{
    $session = session();
    $userId = $session->get('user_id');

    $userModel = new \App\Models\UserModel();
    $applicantModel = new \App\Models\ApplicantModel();

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
        'date_of_birth'       => $this->request->getPost('dob') ?? '',
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
    }

    return $this->response->setJSON([
        'success' => true,
        'message' => 'Personal Information updated successfully!'
    ]);
}

public function updateFamily()
{
    $session = session();
    $userId = $session->get('user_id');

    $applicantFamModel = new ApplicantFamModel();
    $familyMembers = ['Spouse', 'Father', 'Mother'];

    try {
        foreach ($familyMembers as $relation) {
            $key = strtolower($relation);

            // --- Gather data from POST, default to blank ---
            $data = [
                'user_id'     => $userId,
                'relationship'=> $relation,
                'first_name'  => $this->request->getPost($key.'_first_name') ?? '',
                'middle_name' => $this->request->getPost($key.'_middle_name') ?? '',
                'last_name'   => $this->request->getPost($key.'_last_name') ?? '',
                'extension'   => $this->request->getPost($key.'_extension') ?? '',
                'occupation'  => $this->request->getPost($key.'_occupation') ?? '',
                'contact_no'  => $this->request->getPost($key.'_contact_no') ?? '',
            ];

            // --- Validation for contact number ---
            if (!empty($data['contact_no']) && !preg_match('/^\d{11}$/', $data['contact_no'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "$relation Contact No. must be exactly 11 digits."
                ]);
            }

            // --- Check if record exists ---
            $existing = $applicantFamModel
                ->where('user_id', $userId)
                ->where('relationship', $relation)
                ->first();

            if ($existing) {
                // --- Update existing record ---
                $applicantFamModel->update($existing['id'], $data);
            } else {
                // --- Insert new record ---
                $applicantFamModel->insert($data);
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Family background updated successfully.'
        ]);

    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
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
    if (!$this->request->isAJAX()) {
        return redirect()->to('account/personal');
    }

    // Parse JSON
    $education = $this->request->getJSON(true); // associative array

    if (empty($education['education'] ?? [])) {
        return $this->response->setJSON([
            'success' => false, 
            'message' => 'No data received.'
        ]);
    }

    $educationRecords = $education['education'];
    $userId = session()->get('user_id');
    $educationModel = new \App\Models\ApplicantEducationModel();

    $processedIds = [];

    foreach ($educationRecords as $eduData) {
        $data = [
            'user_id' => $userId,
            'degree_level_id' => $eduData['degree_level_id'] ?? null,
            'degree_id' => $eduData['degree_id'] ?? null,
            'school_name' => $eduData['school_name'] ?? null,
            'degree_course' => $eduData['degree_course'] ?? ($eduData['degree_id'] ? $this->getDegreeName($eduData['degree_id']) : null),
            'period_from' => $eduData['period_from'] ?? null,
            'period_to' => $eduData['period_to'] ?? null,
            'highest_level_units' => $eduData['highest_level_units'] ?? null,
            'year_graduated' => $eduData['year_graduated'] ?? null,
            'awards' => $eduData['awards'] ?? null
        ];

        // If ID exists, update; else insert new
        if (!empty($eduData['id'])) {
            $educationModel->update($eduData['id'], $data);
            $processedIds[] = $eduData['id'];
        } else {
            $insertId = $educationModel->insert($data);
            $processedIds[] = $insertId;
        }
    }

    // Return processed IDs so JS can dynamically update the table
    return $this->response->setJSON([
        'success' => true,
        'message' => 'Educational Background updated successfully!',
        'processedIds' => $processedIds
    ]);
}

public function deleteEducation($id = null)
{
    if (!$id) {
        return $this->response->setJSON([
            'success' => false, 
            'message' => 'Invalid ID'
        ]);
    }

    $model = new \App\Models\ApplicantEducationModel();

    try {
        if ($model->delete($id)) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Education record deleted', 
                'id' => $id
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Failed to delete record'
            ]);
        }
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

private function getDegreeName($degreeId)
{
    $degreeModel = new \App\Models\DegreeModel();
    $degree = $degreeModel->find($degreeId);
    return $degree['degree_name'] ?? null;
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

    // Handle file upload
    $certificateFile = $this->request->getFile('certificate');
    $certificateName = null;
    if ($certificateFile && $certificateFile->isValid() && !$certificateFile->hasMoved()) {
        $certificateName = $userId . '_' . time() . '_' . $certificateFile->getRandomName();
        $certificateFile->move(WRITEPATH . 'uploads/civil_service', $certificateName);
    }

    // Required fields: default to 'N/A' if empty
    $eligibility = $this->request->getPost('eligibility') ?: 'N/A';
    $rating = $this->request->getPost('rating') ?: 'N/A';
    $date_of_exam = $this->request->getPost('date_of_exam') ?: null; // date, keep null if empty
    $place_of_exam = $this->request->getPost('place_of_exam') ?: 'N/A';
    $license_no = $this->request->getPost('license_no') ?: 'N/A';

    // Optional field: license_valid_until
    $license_valid_until = $this->request->getPost('license_valid_until');
    if (empty($license_valid_until)) {
        $license_valid_until = null; // will save as NULL in DB
    }

    $data = [
        'user_id' => $userId,
        'eligibility' => $eligibility,
        'rating' => $rating,
        'date_of_exam' => $date_of_exam,
        'place_of_exam' => $place_of_exam,
        'license_no' => $license_no,
        'license_valid_until' => $license_valid_until,
    ];

    // Add certificate if uploaded
    if ($certificateName) {
        $data['certificate'] = $certificateName;
    }

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

public function viewCivilCertificate($filename)
{
    $userId = session()->get('user_id'); // optional: restrict access per user
    $filePath = WRITEPATH . 'uploads/civil_service/' . $filename;

    if (!file_exists($filePath)) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File not found');
    }

    // Determine mime type
    $mime = mime_content_type($filePath);

    return $this->response->setHeader('Content-Type', $mime)
                          ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                          ->setBody(file_get_contents($filePath));
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
public function viewTrainingCertificate($filename)
{
    $filePath = FCPATH . 'writable/uploads/trainings/' . $filename;

    if (!file_exists($filePath)) {
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'File not found or already deleted.'
        ])->setStatusCode(200);
    }

    $mime = mime_content_type($filePath);

    return $this->response
        ->setHeader('Content-Type', $mime)
        ->setHeader('Content-Disposition', 'inline; filename="'.$filename.'"')
        ->setBody(file_get_contents($filePath));
}

public function addApplicantTraining()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON(['success'=>false,'message'=>'Invalid request.']);
    }

    $userId = session()->get('user_id');
    $fileName = null;

    $file = $this->request->getFile('training_certificate_file');

    // Ensure 'uploads/trainings' folder exists
    $uploadPath = FCPATH . 'writable/uploads/trainings/';
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    if ($file && $file->isValid() && !$file->hasMoved()) {
        $fileName = $file->getRandomName();
        $file->move($uploadPath, $fileName);
    }

    $data = [
        'user_id' => $userId,
        'training_name' => $this->request->getPost('training_name'),
        'training_category_id' => $this->request->getPost('training_category_id'),
        'training_venue' => $this->request->getPost('training_venue'),
        'date_from' => $this->request->getPost('date_from'),
        'date_to' => $this->request->getPost('date_to'),
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

    // Ensure 'uploads/trainings' folder exists
    $uploadPath = FCPATH . 'writable/uploads/trainings/';
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    if ($file && $file->isValid() && !$file->hasMoved()) {
        $fileName = $file->getRandomName();
        $file->move($uploadPath, $fileName);
    }

    // Update data
    $data = [
        'training_name' => $this->request->getPost('training_name'),
        'training_category_id' => $this->request->getPost('training_category_id'),
        'training_venue' => $this->request->getPost('training_venue'),
        'date_from' => $this->request->getPost('date_from'),
        'date_to' => $this->request->getPost('date_to'),
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

public function viewFile($filename)
{
    $path = WRITEPATH . 'uploads/files/' . $filename;

    if (!file_exists($path)) {
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'File not found or already deleted.'
        ])->setStatusCode(200);
    }

    return $this->response
        ->setHeader('Content-Type', mime_content_type($path))
        ->setHeader('Content-Disposition', 'inline; filename="'.$filename.'"')
        ->setBody(file_get_contents($path));
}
public function updateFile()
{
    $session = session();
    $userId = $session->get('user_id');

    $fileModel = new \App\Models\ApplicantDocumentsModel();

    $field = $this->request->getPost('file_field'); // e.g., 'pds', 'performance_rating', 'resume', 'tor', 'diploma'
    $allowedFields = ['pds','performance_rating','resume','tor','diploma'];

    if (!$field || !in_array($field, $allowedFields)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid file field.'
        ]);
    }

    $file = $this->request->getFile('file');
    if (!$file || !$file->isValid() || $file->hasMoved()) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'No valid file uploaded.'
        ]);
    }

    helper('filesystem');

    $newName = $file->getRandomName();
    $uploadPath = WRITEPATH.'uploads/files';

    if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);

    $file->move($uploadPath, $newName);

    $uploaded = $fileModel->where('user_id', $userId)->first();

    // Philippine time
    $manilaTime = new \DateTime('now', new \DateTimeZone('Asia/Manila'));

    $data = [
        $field => $newName,
        'uploaded_at' => $manilaTime->format('Y-m-d H:i:s'),
        'updated_at'  => $manilaTime->format('Y-m-d H:i:s')
    ];

    if ($uploaded) {
        $fileModel->update($uploaded['id'], $data);
    } else {
        $data['user_id'] = $userId;
        $data['created_at'] = $manilaTime->format('Y-m-d H:i:s');
        $fileModel->insert($data);
    }

    return $this->response->setJSON([
        'status' => 'success',
        'message' => ucfirst(str_replace('_',' ',$field)).' updated successfully!',
        'file_name' => $newName,
        'file_url'  => base_url('writable/uploads/files/'.$newName)
    ]);
}

public function deleteFile()
{
    $session = session();
    $userId = $session->get('user_id');

    $fileField = $this->request->getPost('file_field');

    // Allow all your uploaded file fields
    $allowedFields = ['pds', 'performance_rating', 'resume', 'tor', 'diploma', 'certificate'];
    if (!$fileField || !in_array($fileField, $allowedFields)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid file field.'
        ]);
    }

    $fileModel = new \App\Models\ApplicantDocumentsModel();
    $uploaded = $fileModel->where('user_id', $userId)->first();

    if (!$uploaded || empty($uploaded[$fileField])) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'No file to delete.'
        ]);
    }

    // Delete file from server
    $filePath = FCPATH . 'uploads/' . $uploaded[$fileField];
    if (is_file($filePath)) {
        unlink($filePath);
    }

    // Update DB
    $fileModel->update($uploaded['id'], [
        $fileField => null,
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    return $this->response->setJSON([
        'status' => 'success',
        'message' => ucfirst($fileField).' deleted successfully!'
    ]);
}

}
