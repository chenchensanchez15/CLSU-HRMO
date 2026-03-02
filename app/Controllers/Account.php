<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ApplicantModel;
use App\Models\JobApplicationModel;
use App\Models\ApplicantDocumentsModel;
use App\Models\ApplicantCivilServiceModel;
use App\Models\ApplicantTrainingModel;
use setasign\Fpdi\Fpdi;
use Exception;

class Account extends BaseController
{
    
public function personal()
{
    $session = session();
    
    // 🔒 Authentication check
    if (!$session->get('logged_in')) {
        return redirect()->to('/login');
    }
    
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
    $documentTypeModel     = new \App\Models\DocumentTypeModel();
    $jobApplicationModel   = new \App\Models\JobApplicationModel();
    $jobPublicationReqModel = new \App\Models\JobPublicationRequirementsModel();
    // Family background functionality removed

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

    // Family background functionality removed

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
// ----------------- CIVIL SERVICE CERTIFICATES COUNT -----------------
$civilCertificatesCount = $civilModel
    ->where('user_id', $userId)
    ->where('certificate IS NOT NULL')
    ->countAllResults();

// ----------------- TRAINING CERTIFICATES COUNT -----------------
$trainingModelForCount = new \App\Models\ApplicantTrainingModel();
$trainingCertificatesCount = $trainingModelForCount
    ->where('user_id', $userId)
    ->where('certificate_file IS NOT NULL')
    ->countAllResults();

// ----------------- FILES -----------------
// Get all document types
$documentTypes = $documentTypeModel->getAllDocumentTypes();

// Get user's documents
$userDocuments = $fileModel->getDocumentsByUser($userId);

// Create associative array for easy access
$fileRecords = [];
foreach ($userDocuments as $doc) {
    $fileRecords[$doc['document_type_id']] = $doc['filename'];
}

// Get current job application and its requirements
$currentApplication = $jobApplicationModel
    ->where('user_id', $userId)
    ->where('application_status', 'draft')
    ->orderBy('created_at', 'DESC')
    ->first();

$requiredDocuments = [];
if ($currentApplication) {
    $requiredDocuments = $jobPublicationReqModel->getRequirementsByVacancy($currentApplication['job_vacancy_id']);
}

// If no application or no requirements, show all document types
if (empty($requiredDocuments)) {
    $requiredDocuments = $documentTypes;
}

// Get certificates from trainings and civil service
$trainingModel = new \App\Models\ApplicantTrainingModel();
$civilServiceModel = new \App\Models\ApplicantCivilServiceModel();

// Get training certificates
$trainingCertificates = $trainingModel
    ->where('user_id', $userId)
    ->where('certificate_file IS NOT NULL')
    ->where('certificate_file !=', '')
    ->findAll();

// Get civil service certificates
$civilServiceCertificates = $civilServiceModel
    ->where('user_id', $userId)
    ->where('certificate IS NOT NULL')
    ->where('certificate !=', '')
    ->findAll();

// Add certificate information to file records for display
$certificateInfo = [
    'training_certificates' => $trainingCertificates,
    'civil_service_certificates' => $civilServiceCertificates
];

    return view('account/personal', [
        'user'                  => $user,
        'profile'               => $profile,
        'educationRecords'      => $finalEducation,
        'workRecords'           => $workRecords,
        'civilRecords'          => $civilRecords,
        'trainingRecords'       => $trainingRecords,
        'trainingCategories'    => $trainingCategories,
        'totalTrainingDuration' => $totalDuration, // pass total duration to view
        'fileRecords'           => $fileRecords,
        'documentTypes'         => $documentTypes,
        'requiredDocuments'     => $requiredDocuments,
        'certificateInfo'       => $certificateInfo,
        'civilCertificatesCount'=> $civilCertificatesCount,
        'trainingCertificatesCount'=> $trainingCertificatesCount,
        'libDegrees'            => $libDegrees,
        'libDegreeLevels'       => $libDegreeLevels,
    ]);
}

public function update()
{
    $session = session();
    
    // 🔒 Authentication check
    if (!$session->get('logged_in')) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Authentication required.'
        ])->setStatusCode(401);
    }
    
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

// Family background functionality removed
public function updatePhoto()
{
    $session = session();
    
    // 🔒 Authentication check
    if (!$session->get('logged_in')) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Authentication required.'
        ])->setStatusCode(401);
    }
    
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
        
        // 🔒 Authentication check
        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }
        
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
        
        // 🔒 Authentication check
        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }
        
        $userId = $session->get('user_id');

        $current = $this->request->getPost('current_password');
        $new = $this->request->getPost('new_password');
        $confirm = $this->request->getPost('confirm_password');

        // Basic validation
        if (empty($current)) {
            return redirect()->back()->with('error', 'Current password is required.');
        }
        
        if (empty($new)) {
            return redirect()->back()->with('error', 'New password is required.');
        }
        
        if (strlen($new) < 8) {
            return redirect()->back()->with('error', 'New password must be at least 8 characters long.');
        }

        // Verify current password FIRST (critical security check)
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!password_verify($current, $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        // Only validate new password confirmation AFTER current password is verified
        if ($new !== $confirm) {
            return redirect()->back()->with('error', 'New password and confirm password do not match.');
        }

        // Update password only if all validations pass
        $userModel->update($userId, [
            'password' => password_hash($new, PASSWORD_DEFAULT),
            'first_login' => 0
        ]);

        $session->set('first_login', 0);

        // Send email notification
        $this->sendPasswordChangeEmail($user['email'], $user['first_name']);

        // Stay on the same page after successful update
        return redirect()->back()->with('success', 'Password updated successfully!');
    }
    
    /**
     * Send password change notification email
     */
    private function sendPasswordChangeEmail($email, $firstName)
    {
        $emailService = \Config\Services::email();
        
        // Email content
        $subject = 'Password Changed - CLSU HRMO';
        $message = $this->getEmailTemplate($firstName);
        
        // Configure email
        $emailService->setTo($email);
        $emailService->setFrom('rogelioalmerol1@gmail.com', 'CLSU HRMO');
        $emailService->setSubject($subject);
        $emailService->setMessage($message);
        
        // Send email
        if (!$emailService->send()) {
            // Log error but don't interrupt user flow
            log_message('error', 'Failed to send password change email to: ' . $email);
        }
    }
    
    /**
     * Get email template
     */
    private function getEmailTemplate($firstName)
    {
        // Convert to Philippine time
        $utcDateTime = new \DateTime('now', new \DateTimeZone('UTC'));
        $philippineTimeZone = new \DateTimeZone('Asia/Manila');
        $utcDateTime->setTimezone($philippineTimeZone);
        $currentTime = $utcDateTime->format('F j, Y g:i A');
        
        $template = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #0B6B3A; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .warning { background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CLSU Human Resource Management Office</h1>
            <p>Online Job Application System</p>
        </div>
        
        <div class="content">
            <h2>Hello ' . $firstName . '!</h2>
            
            <p>Your password has been successfully changed on <strong>' . $currentTime . '</strong>.</p>
            
            <div class="warning">
                <strong>⚠ Security Notice:</strong><br>
                If you did not make this change, please contact our support team immediately.
            </div>
            
            <p>If you have any questions or concerns, please don\'t hesitate to reach out to us.</p>
            
            <p>Best regards,<br>
            <strong>CLSU HRMO Team</strong></p>
        </div>
        
        <div class="footer">
            &copy; 2026 CLSU-HRMO. All rights reserved.<br>
            Powered by Management Information System Office (CLSU-MISO)
        </div>
    </div>
</body>
</html>';
        
        return $template;
    }
    
public function updateEducation()
{
    $session = session();
    
    // 🔒 Authentication check
    if (!$session->get('logged_in')) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Authentication required.'
        ])->setStatusCode(401);
    }
    
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
            'degree_course' => $eduData['degree_id'] ? $this->getDegreeName($eduData['degree_id']) : null,
            'course' => $eduData['course_name'] ?? null,
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
    $session = session();
    
    // 🔒 Authentication check
    if (!$session->get('logged_in')) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Authentication required.'
        ])->setStatusCode(401);
    }
    
    if (!$this->request->isAJAX()) {
        return redirect()->to('account/personal');
    }

    // Get the single work entry from the modal form
    $id = $this->request->getPost('id');
    $userId = $session->get('user_id');

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
    $session = session();
    
    // 🔒 Authentication check
    if (!$session->get('logged_in')) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Authentication required.'
        ])->setStatusCode(401);
    }
    
    if (!$this->request->isAJAX()) {
        return redirect()->to('account/personal');
    }

    $userId = $session->get('user_id');
    $civilModel = new \App\Models\ApplicantCivilServiceModel();

    $id = $this->request->getPost('id'); // null if adding new

$certificateFile = $this->request->getFile('certificate');
$certificateName = null;

if ($certificateFile && $certificateFile->isValid() && !$certificateFile->hasMoved()) {

    // 🔒 Validate MIME type (REAL file type, not just extension)
    if ($certificateFile->getMimeType() !== 'application/pdf') {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Only PDF files are allowed.'
        ]);
    }

    // 🔒 Validate extension
    if (strtolower($certificateFile->getExtension()) !== 'pdf') {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid file format. PDF only.'
        ]);
    }

    // 🔒 Validate file size (5MB limit)
    if ($certificateFile->getSize() > 5 * 1024 * 1024) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'File size must not exceed 5MB.'
        ]);
    }

    // ✅ Generate secure filename
    $certificateName = $userId . '_' . time() . '_' . $certificateFile->getRandomName();

    // ✅ Move file safely
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
        
        //✅ Automatically save to applicant_documents table
        $fileModel = new \App\Models\ApplicantDocumentsModel();
        $fileModel->saveDocument($userId, 3, $certificateName); // document_type_id = 3 for Certificate of Eligibility
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

    $userId = session()->get('user_id');
    $civilModel = new \App\Models\ApplicantCivilServiceModel();
    $fileModel = new \App\Models\ApplicantDocumentsModel();

    // Get the certificate filename before deleting the record
    $civilRecord = $civilModel->find($id);
    $certificateFile = $civilRecord['certificate'] ?? null;

    if ($civilModel->delete($id)) {
        //✅ Also delete from applicant_documents table
        if ($certificateFile) {
            $fileModel->deleteDocument($userId, 3); // document_type_id = 3 for Certificate of Eligibility
            
            // Delete physical file
            $filePath = WRITEPATH . 'uploads/civil_service/' . $certificateFile;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
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

public function viewCivilCertificate($filename = null)
{
    // No filename provided
    if (!$filename) {
        return $this->response->setStatusCode(404)
                              ->setJSON([
                                  'status' => 'warning',
                                  'message' => 'No civil service certificate has been uploaded for this record.'
                              ]);
    }

    $filename = urldecode($filename);
    $filePath = WRITEPATH . 'uploads/civil_service/' . $filename;

    // File does not exist
    if (!file_exists($filePath)) {
        return $this->response->setStatusCode(404)
                              ->setJSON([
                                  'status' => 'warning',
                                  'message' => 'No civil service certificate has been uploaded for this record.'
                              ]);
    }

    // File exists → stream PDF inline
    return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->setHeader('Accept-Ranges', 'bytes')
                ->setBody(file_get_contents($filePath));
}

public function viewEligibilityCertificates()
{
    $session = session();

    if (!$session->get('logged_in')) {
        return $this->response->setStatusCode(401)
                              ->setJSON(['message' => 'Unauthorized']);
    }

    $userId = $session->get('user_id');

    $civilModel = new ApplicantCivilServiceModel();

    $certificates = $civilModel
        ->where('user_id', $userId)
        ->where('certificate IS NOT NULL')
        ->where('certificate !=', '')
        ->findAll();

    if (empty($certificates)) {
        // Return a simple PDF with message when no certificates found
        $pdf = new Fpdi();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'No Civil Service Certificates Found', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(10);
        $pdf->MultiCell(0, 10, 'You have not uploaded any civil service eligibility certificates yet.', 0, 'C');
        
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="No_Certificates.pdf"')
            ->setBody($pdf->Output('S'));
    }

    $pdf = new Fpdi();
    $hasValidCertificates = false;

    foreach ($certificates as $cert) {
        $filePath = WRITEPATH . 'uploads/civil_service/' . $cert['certificate'];

        if (file_exists($filePath) && is_readable($filePath)) {
            try {
                $pageCount = $pdf->setSourceFile($filePath);
                
                if ($pageCount > 0) {
                    $hasValidCertificates = true;
                    
                    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                        $templateId = $pdf->importPage($pageNo);
                        $size = $pdf->getTemplateSize($templateId);
                        
                        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $pdf->useTemplate($templateId);
                    }
                }
            } catch (Exception $e) {
                // Skip invalid PDF files
                continue;
            }
        }
    }

    // If no valid certificates were found, return empty PDF
    if (!$hasValidCertificates) {
        $pdf = new Fpdi();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'No Valid Civil Service Certificates', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(10);
        $pdf->MultiCell(0, 10, 'None of the uploaded certificate files are valid PDF documents.', 0, 'C');
    }

    return $this->response
        ->setHeader('Content-Type', 'application/pdf')
        ->setHeader('Content-Disposition', 'inline; filename="Civil_Service_Certificates.pdf"')
        ->setBody($pdf->Output('S'));
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

public function viewTrainingCertificate($filename = null)
{
    if (!$filename) {
        return $this->response->setStatusCode(404)
                              ->setJSON([
                                  'status' => 'warning',
                                  'message' => 'No training certificate has been uploaded for this record.'
                              ]);
    }

    $filename = urldecode($filename);
    $filePath = FCPATH . 'writable/uploads/trainings/' . $filename;

    if (!file_exists($filePath)) {
        return $this->response->setStatusCode(404)
                              ->setJSON([
                                  'status' => 'warning',
                                  'message' => 'No training certificate has been uploaded for this record.'
                              ]);
    }

    return $this->response
                ->setHeader('Content-Type', mime_content_type($filePath))
                ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->setHeader('Accept-Ranges', 'bytes')
                ->setBody(file_get_contents($filePath));
}

public function viewTrainingCertificates()
{
    $session = session();

    if (!$session->get('logged_in')) {
        return $this->response->setStatusCode(401)
                              ->setJSON(['message' => 'Unauthorized']);
    }

    $userId = $session->get('user_id');

    $trainingModel = new \App\Models\ApplicantTrainingModel();

    $certificates = $trainingModel
        ->where('user_id', $userId)
        ->where('certificate_file IS NOT NULL')
        ->where('certificate_file !=', '')
        ->findAll();

    if (empty($certificates)) {
        // Return a simple PDF with message when no certificates found
        $pdf = new \setasign\Fpdi\Fpdi();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'No Training Certificates Found', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(10);
        $pdf->MultiCell(0, 10, 'You have not uploaded any training certificates yet.', 0, 'C');
        
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="No_Training_Certificates.pdf"')
            ->setBody($pdf->Output('S'));
    }

    $pdf = new \setasign\Fpdi\Fpdi();
    $hasValidCertificates = false;

    foreach ($certificates as $cert) {
        $filePath = WRITEPATH . 'uploads/trainings/' . $cert['certificate_file'];

        if (file_exists($filePath) && is_readable($filePath)) {
            try {
                $pageCount = $pdf->setSourceFile($filePath);
                
                if ($pageCount > 0) {
                    $hasValidCertificates = true;
                    
                    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                        $templateId = $pdf->importPage($pageNo);
                        $size = $pdf->getTemplateSize($templateId);
                        
                        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $pdf->useTemplate($templateId);
                    }
                }
            } catch (Exception $e) {
                // Skip invalid PDF files
                continue;
            }
        }
    }

    // If no valid certificates were found, return empty PDF
    if (!$hasValidCertificates) {
        $pdf = new \setasign\Fpdi\Fpdi();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'No Valid Training Certificates', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(10);
        $pdf->MultiCell(0, 10, 'None of the uploaded certificate files are valid PDF documents.', 0, 'C');
    }

    return $this->response
        ->setHeader('Content-Type', 'application/pdf')
        ->setHeader('Content-Disposition', 'inline; filename="Training_Certificates.pdf"')
        ->setBody($pdf->Output('S'));
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

    // 🔒 MIME type validation
    if (!in_array($file->getMimeType(), [
        'application/pdf',
        'application/x-pdf'
    ])) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Only PDF files are allowed.'
        ]);
    }

    // 🔒 Extension validation
    if (strtolower($file->getExtension()) !== 'pdf') {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid file format. PDF only.'
        ]);
    }

    // 🔒 Size limit (5MB)
    if ($file->getSize() > 5 * 1024 * 1024) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'File must not exceed 5MB.'
        ]);
    }

    $fileName = $userId . '_' . time() . '_' . $file->getRandomName();
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

    //✅ Automatically save to applicant_documents table
    if ($fileName) {
        $fileModel = new \App\Models\ApplicantDocumentsModel();
        $fileModel->saveDocument($userId, 7, $fileName); // document_type_id = 7 for Certificate of Trainings and Seminars
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

    if (!in_array($file->getMimeType(), [
        'application/pdf',
        'application/x-pdf'
    ])) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Only PDF files are allowed.'
        ]);
    }

    if (strtolower($file->getExtension()) !== 'pdf') {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid file format. PDF only.'
        ]);
    }

    if ($file->getSize() > 5 * 1024 * 1024) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'File must not exceed 5MB.'
        ]);
    }

    // 🔥 Delete old file if exists
    if (!empty($training['certificate_file'])) {
        $oldPath = $uploadPath . $training['certificate_file'];
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
    }

    $fileName = $userId . '_' . time() . '_' . $file->getRandomName();
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

    //✅ Automatically save/update in applicant_documents table
    if ($fileName) {
        $fileModel = new \App\Models\ApplicantDocumentsModel();
        $fileModel->saveDocument($userId, 7, $fileName); // document_type_id = 7 for Certificate of Trainings and Seminars
    }

    return $this->response->setJSON([
        'success' => true,
        'message' => 'Training updated successfully!'
    ]);
}
    public function deleteTraining($id)
    {
        $userId = session()->get('user_id');
        $trainingModel = new ApplicantTrainingModel();
        $fileModel = new \App\Models\ApplicantDocumentsModel();
        
        // Get the certificate filename before deleting the record
        $trainingRecord = $trainingModel->find($id);
        $certificateFile = $trainingRecord['certificate_file'] ?? null;
        
        if ($trainingModel->delete($id)) {
            //✅ Also delete from applicant_documents table
            if ($certificateFile) {
                $fileModel->deleteDocument($userId, 7); // document_type_id = 7 for Certificate of Trainings and Seminars
                
                // Delete physical file
                $filePath = WRITEPATH . 'uploads/trainings/' . $certificateFile;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
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
    // Check multiple possible locations for the file
    $possiblePaths = [
        WRITEPATH . 'uploads/files/' . $filename,  // Main document uploads
        FCPATH . 'uploads/' . $filename,           // Legacy uploads
        WRITEPATH . 'uploads/' . $filename         // Training/civil service certificates
    ];
    
    $path = null;
    foreach ($possiblePaths as $possiblePath) {
        if (file_exists($possiblePath)) {
            $path = $possiblePath;
            break;
        }
    }

    if (!$path) {
        return $this->response->setJSON([
            'status'  => 'warning',
            'message' => 'No file has been uploaded for this document.'
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
    
    // 🔒 Authentication check
    if (!$session->get('logged_in')) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Authentication required.'
        ])->setStatusCode(401);
    }
    
    $userId = $session->get('user_id');

    $fileModel = new \App\Models\ApplicantDocumentsModel();
    $documentTypeModel = new \App\Models\DocumentTypeModel();

    $documentTypeId = $this->request->getPost('document_type_id');
    
    // Validate document type ID
    if (!$documentTypeId) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Document type is required.'
        ]);
    }

    $documentType = $documentTypeModel->find($documentTypeId);
    if (!$documentType) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid document type.'
        ]);
    }

    $file = $this->request->getFile('file');
    if (!$file || !$file->isValid() || $file->hasMoved()) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'No valid file uploaded.'
        ]);
    }

    // Check file type (PDF only)
    $allowedTypes = ['application/pdf'];
    if (!in_array($file->getMimeType(), $allowedTypes)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Only PDF files are allowed.'
        ]);
    }

    // Check file size (5MB limit)
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    if ($file->getSize() > $maxFileSize) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'File size must not exceed 5 MB.'
        ]);
    }

    helper('filesystem');

    $newName = $file->getRandomName();
    $uploadPath = WRITEPATH.'uploads/files';

    if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);

    $file->move($uploadPath, $newName);

    // Save or update document using the new model method
    $result = $fileModel->saveDocument($userId, $documentTypeId, $newName);

    if ($result) {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => $documentType['document_type_name'].' updated successfully!',
            'file_name' => $newName,
            'file_url'  => base_url('writable/uploads/files/'.$newName)
        ]);
    } else {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to save document.'
        ]);
    }
}

public function deleteFile()
{
    $session = session();
    
    // 🔒 Authentication check
    if (!$session->get('logged_in')) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Authentication required.'
        ])->setStatusCode(401);
    }
    
    $userId = $session->get('user_id');

    $documentTypeId = $this->request->getPost('document_type_id');
    
    if (!$documentTypeId) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Document type is required.'
        ]);
    }

    $fileModel = new \App\Models\ApplicantDocumentsModel();
    $documentTypeModel = new \App\Models\DocumentTypeModel();
    
    $document = $fileModel->getDocumentByType($userId, $documentTypeId);
    
    if (!$document) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'No file to delete.'
        ]);
    }
    
    $documentType = $documentTypeModel->find($documentTypeId);
    if (!$documentType) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid document type.'
        ]);
    }

    // Delete file from server
    $filePath = WRITEPATH . 'uploads/files/' . $document['filename'];
    if (is_file($filePath)) {
        unlink($filePath);
    }

    // Delete from DB
    $result = $fileModel->deleteDocument($userId, $documentTypeId);

    if ($result) {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => $documentType['document_type_name'].' deleted successfully!'
        ]);
    } else {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to delete document.'
        ]);
    }
}

}
