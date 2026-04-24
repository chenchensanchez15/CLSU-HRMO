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

    // Check if there's a pending redirect after profile completion
    $redirectAfterProfileComplete = $session->get('redirect_after_profile_complete');
    if ($redirectAfterProfileComplete) {
        // Store it for later use after profile update
        $session->set('pending_redirect_url', $redirectAfterProfileComplete);
        $session->remove('redirect_after_profile_complete');
    }

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
            'permanent_address' => '',
            'photo' => ''
        ];
    }
    
    // ----------------- GOOGLE DRIVE PHOTO -----------------
    // Check if photo is stored in Google Drive (Google Drive file ID format)
    $photoUrl = null;
    $hasGoogleDrivePhoto = false;
    
    if (!empty($profile['photo'])) {
        // Check if it's a Google Drive file ID (alphanumeric, 20+ chars, no timestamp prefix)
        $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $profile['photo']) && !preg_match('/^\d{10}_/', $profile['photo']);
        
        if ($isGoogleDriveFile) {
            log_message('debug', 'Photo found in Google Drive: ' . $profile['photo']);
            $fileId = $profile['photo'];
            
            // Try 1: OAuth method (primary)
            try {
                $driveService = new \App\Libraries\GoogleDriveOAuthService();
                
                if ($driveService->isAuthenticated()) {
                    $client = $driveService->getClient();
                    $accessToken = $client->getAccessToken()['access_token'];
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/drive/v3/files/' . $fileId . '?fields=id,name,mimeType,webContentLink&supportsAllDrives=true');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Authorization: Bearer ' . $accessToken
                    ]);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    if ($httpCode == 200) {
                        $fileData = json_decode($response, true);
                        if (isset($fileData['id'])) {
                            $photoUrl = 'https://www.googleapis.com/drive/v3/files/' . $fileId . '?alt=media';
                            $hasGoogleDrivePhoto = true;
                            log_message('info', 'Successfully retrieved Google Drive photo (OAuth) for user ' . $userId);
                        }
                    }
                }
            } catch (\Exception $e) {
                log_message('warning', 'OAuth photo retrieval failed: ' . $e->getMessage());
            }
            
            // Try 2: Service account fallback if OAuth fails
            if (!$hasGoogleDrivePhoto) {
                try {
                    $serviceAccountPath = WRITEPATH . 'credentials/google_credentials.json';
                    if (file_exists($serviceAccountPath)) {
                        $client = new \Google\Client();
                        $client->setAuthConfig($serviceAccountPath);
                        $client->addScope([\Google\Service\Drive::DRIVE]);
                        
                        $accessToken = $client->fetchAccessTokenWithAssertion();
                        
                        if (isset($accessToken['access_token'])) {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/drive/v3/files/' . $fileId . '?fields=id,name,mimeType&supportsAllDrives=true');
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                'Authorization: Bearer ' . $accessToken['access_token']
                            ]);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            
                            $response = curl_exec($ch);
                            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            curl_close($ch);
                            
                            if ($httpCode == 200) {
                                $fileData = json_decode($response, true);
                                if (isset($fileData['id'])) {
                                    $photoUrl = 'https://www.googleapis.com/drive/v3/files/' . $fileId . '?alt=media';
                                    $hasGoogleDrivePhoto = true;
                                    log_message('info', 'Successfully retrieved Google Drive photo (Service Account) for user ' . $userId);
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Service account photo retrieval failed: ' . $e->getMessage());
                }
            }
        }
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
$latestUpdatedAt = null; // Track the most recent update time across all documents

foreach ($userDocuments as $doc) {
    $fileRecords[$doc['document_type_id']] = $doc['filename'];
    
    // Track the latest updated_at timestamp
    $currentDocTime = null;
    if (isset($doc['updated_at']) && !empty($doc['updated_at']) && $doc['updated_at'] != '0000-00-00 00:00:00') {
        $currentDocTime = $doc['updated_at'];
    } elseif (isset($doc['created_at']) && !empty($doc['created_at']) && $doc['created_at'] != '0000-00-00 00:00:00') {
        $currentDocTime = $doc['created_at'];
    }
    
    // Update latest timestamp if this document is more recent
    if ($currentDocTime && (!$latestUpdatedAt || strtotime($currentDocTime) > strtotime($latestUpdatedAt))) {
        $latestUpdatedAt = $currentDocTime;
    }
}

// Store the latest timestamp
if ($latestUpdatedAt) {
    $fileRecords['uploaded_at'] = $latestUpdatedAt;
}

// Check if Google Drive is authenticated
$googleDriveAuthenticated = false;
$googleDriveFiles = [];
$driveService = null;

try {
    $driveService = new \App\Libraries\GoogleDriveOAuthService();
    $googleDriveAuthenticated = $driveService->isEnabled();
    
    if ($googleDriveAuthenticated) {
        log_message('debug', 'Google Drive is authenticated for user ' . $userId);
        
        // Fetch files from Google Drive if available
        try {
            // Initialize Google Drive client
            $client = $driveService->getClient();
            $accessToken = $client->getAccessToken()['access_token'];
            
            // Search for files in the configured folder
            $folderId = $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? null;
            
            if ($folderId) {
                // Build query to search for files by name pattern (user_id_timestamp_*)
                $query = "'" . $folderId . "' in parents and name contains '" . $userId . "_'";
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/drive/v3/files?q=' . urlencode($query) . '&fields=files(id,name,mimeType,createdTime,modifiedTime)&supportsAllDrives=true');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $accessToken
                ]);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($httpCode == 200) {
                    $data = json_decode($response, true);
                    $googleDriveFiles = $data['files'] ?? [];
                    
                    // Log found files for debugging
                    log_message('debug', 'Found ' . count($googleDriveFiles) . ' files in Google Drive for user ' . $userId);
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Error fetching Google Drive files: ' . $e->getMessage());
        }
    } else {
        log_message('warning', 'Google Drive is not authenticated for user ' . $userId);
    }
} catch (\Exception $e) {
    log_message('error', 'Error initializing Google Drive service: ' . $e->getMessage());
    $googleDriveAuthenticated = false;
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
        'googleDriveFiles'      => $googleDriveFiles,
        'googleDriveAuthenticated' => $googleDriveAuthenticated,
        'photoUrl'              => $photoUrl,
        'hasGoogleDrivePhoto'   => $hasGoogleDrivePhoto,
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

    // Check if there's a pending redirect URL (user was filling profile to apply)
    $pendingRedirect = $session->get('pending_redirect_url');
    
    // Extract vacancy ID from the redirect URL if it exists
    $vacancyId = null;
    if ($pendingRedirect && preg_match('#/applications/apply/(\d+)#', $pendingRedirect, $matches)) {
        $vacancyId = $matches[1];
    }
    
    // Prepare response data
    $responseData = [
        'success' => true,
        'message' => 'Personal Information updated successfully!',
        'vacancy_id' => $vacancyId // Extracted ID for form submission
    ];
    
    // If there's a pending redirect (user was in the middle of applying), 
    // redirect directly to dashboard instead of showing confirmation dialog
    if ($pendingRedirect) {
        $responseData['redirect_url'] = base_url('/dashboard');
        $responseData['message'] = 'Successful. Personal Information completed!';
    } else {
        // If no pending redirect, redirect to dashboard (not home) so user can see full system
        $responseData['redirect_url'] = base_url('/dashboard');
    }
    
    return $this->response->setJSON($responseData);
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

    // Upload to Google Drive using service account - NO LOCAL FALLBACK
    $driveService = new \App\Libraries\GoogleDriveOAuthService();
    
    if (!$driveService->isAuthenticated()) {
        log_message('warning', 'Google Drive not authenticated for photo upload, user ID: ' . $userId);
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Google Drive authentication required. Please connect your Google Drive account first.',
            'auth_required' => true,
            'auth_url' => site_url('google/drive')
        ])->setStatusCode(401);
    }
    
    try {
        log_message('info', 'Uploading profile photo to Google Drive for user ' . $userId);
        
        // Get existing photo before uploading new one
        $existingProfile = $applicantModel->where('user_id', $userId)->first();
        $oldPhotoFileId = $existingProfile['photo'] ?? null;
        
        // Get file content
        $filePath = $photoFile->getTempName();
        // Use consistent naming: {timestamp}_profile_photo.{ext}
        $extension = $photoFile->getClientExtension();
        $fileName = time() . '_profile_photo.' . $extension;
        
        // Upload to Google Drive
        $googleFileId = $driveService->uploadFile($filePath, $fileName, $photoFile->getMimeType());
        
        // Make the photo publicly accessible
        try {
            $client = $driveService->getClient();
            $accessToken = $client->getAccessToken()['access_token'];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/drive/v3/files/' . $googleFileId . '/permissions?supportsAllDrives=true');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'type' => 'anyone',
                'role' => 'reader'
            ]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode == 200) {
                log_message('info', 'Photo permissions set to public');
            }
        } catch (\Exception $e) {
            log_message('warning', 'Could not set photo permissions: ' . $e->getMessage());
        }
        
        // Delete old photo from Google Drive if exists
        if (!empty($oldPhotoFileId)) {
            // Check if it's a Google Drive file ID
            $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $oldPhotoFileId) 
                                 && !preg_match('/^\d{10}_/', $oldPhotoFileId);
            
            if ($isGoogleDriveFile) {
                try {
                    $driveService->deleteFile($oldPhotoFileId);
                    log_message('info', 'Old profile photo deleted from Google Drive: ' . $oldPhotoFileId);
                } catch (\Exception $e) {
                    log_message('warning', 'Could not delete old photo from Google Drive: ' . $e->getMessage());
                }
            } else {
                // Delete local file (legacy support)
                $localPath = WRITEPATH . 'uploads/' . $oldPhotoFileId;
                if (file_exists($localPath)) {
                    unlink($localPath);
                    log_message('info', 'Old profile photo deleted from local storage: ' . $oldPhotoFileId);
                }
            }
        }
        
        // Update database with Google Drive file ID
        if ($existingProfile) {
            $applicantModel->update($existingProfile['id'], ['photo' => $googleFileId]);
        } else {
            $applicantModel->insert([
                'user_id' => $userId,
                'photo' => $googleFileId
            ]);
        }
        
        log_message('info', 'Profile photo uploaded to Google Drive successfully. File ID: ' . $googleFileId);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Profile photo uploaded to Google Drive successfully!',
            'photo'   => $googleFileId,
            'photo_url' => 'https://www.googleapis.com/drive/v3/files/' . $googleFileId . '?alt=media'
        ]);
        
    } catch (\Exception $e) {
        log_message('error', 'Error uploading photo to Google Drive: ' . $e->getMessage());
        log_message('debug', 'Exception trace: ' . $e->getTraceAsString());
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to upload photo to Google Drive: ' . $e->getMessage()
        ])->setStatusCode(500);
    }
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

        $passwordHash = isset($user['password']) ? (string) $user['password'] : '';
        if (!$user || !password_verify($current, $passwordHash)) {
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

        // Check if this was a first-login password change (from Auth controller)
        $profileCompletionRequired = $session->get('profile_completion_required');
        
        // Check if there's a redirect URL stored (from first login flow)
        $redirectAfterPasswordChange = $session->get('redirect_after_password_change');
        
        if ($profileCompletionRequired || $redirectAfterPasswordChange) {
            // Clear session flags
            if ($profileCompletionRequired) {
                $session->remove('profile_completion_required');
            }
            if ($redirectAfterPasswordChange) {
                // Clear the session variable and redirect to profile to fill details first
                $session->remove('redirect_after_password_change');
                // Store the redirect URL for later use after profile completion
                $session->set('redirect_after_profile_complete', $redirectAfterPasswordChange);
            }
            return redirect()->to('/account/personal')->with('fill_details_required', true);
        }

        // Normal password change - stay on the same page
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

    // Upload to Google Drive ONLY (no local storage)
    $driveService = new \App\Libraries\GoogleDriveOAuthService();
    
    if (!$driveService->isAuthenticated()) {
        log_message('warning', 'Google Drive not authenticated for user ID: ' . $userId);
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Google Drive authentication required. Please connect your Google Drive account first.',
            'auth_required' => true,
            'auth_url' => site_url('google/drive')
        ])->setStatusCode(401);
    }

    try {
        // Create temporary file
        $tempDir = WRITEPATH . 'temp/';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        // Use consistent naming: {timestamp}_{original_name}
        $extension = $certificateFile->getClientExtension();
        $baseName = pathinfo($certificateFile->getClientName(), PATHINFO_FILENAME);
        // Sanitize filename: remove special characters but keep underscores
        $sanitizedBaseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
        $googleDriveFileName = time() . '_' . $sanitizedBaseName . '.' . $extension;
        
        $tempPath = $tempDir . $googleDriveFileName;
        $fileContent = file_get_contents($certificateFile->getTempName());
        file_put_contents($tempPath, $fileContent);
        
        log_message('debug', 'Uploading civil service certificate to Google Drive: ' . $googleDriveFileName);
        
        // Upload to Google Drive
        $googleFileId = $driveService->uploadFile($tempPath, $googleDriveFileName, $certificateFile->getMimeType());
        
        // Clean up temp file
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }
        
        $certificateName = $googleFileId;
        log_message('info', 'Civil service certificate uploaded to Google Drive. File ID: ' . $googleFileId);
        
    } catch (\Exception $e) {
        log_message('error', 'Civil service certificate upload failed: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to upload certificate to Google Drive: ' . $e->getMessage()
        ])->setStatusCode(500);
    }
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
            
            // Check if it's a Google Drive file ID
            $certificateFileStr = (string) $certificateFile;
            $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $certificateFileStr) 
                                 && !preg_match('/^\d{10}_/', $certificateFileStr);
            
            if ($isGoogleDriveFile) {
                // Delete from Google Drive
                try {
                    $driveService = new \App\Libraries\GoogleDriveOAuthService();
                    
                    if ($driveService->isAuthenticated()) {
                        $driveService->deleteFile($certificateFile);
                        log_message('info', 'Civil service certificate deleted from Google Drive: ' . $certificateFile);
                    } else {
                        log_message('warning', 'Google Drive not authenticated, could not delete file: ' . $certificateFile);
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Failed to delete civil service certificate from Google Drive: ' . $e->getMessage());
                }
            } else {
                // Delete local file (legacy support)
                $filePath = WRITEPATH . 'uploads/civil_service/' . $certificateFile;
                if (file_exists($filePath)) {
                    unlink($filePath);
                    log_message('info', 'Civil service certificate deleted from local storage: ' . $certificateFile);
                }
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
        
    // Check if it's a Google Drive file ID
    $isGoogleDrive = preg_match('/^[a-zA-Z0-9_-]{28,33}$/', $filename) && !preg_match('/^\d{10}_/', $filename);
        
    if ($isGoogleDrive) {
        // File is stored in Google Drive - download and serve it
        $driveService = new \App\Libraries\GoogleDriveOAuthService();
            
        if ($driveService->isEnabled()) {
            try {
                // Create temp file
                $tempFile = sys_get_temp_dir() . '/' . uniqid('gdrive_') . '.pdf';
                    
                // Download from Google Drive
                $driveService->downloadFile($filename, $tempFile);
                    
                if (file_exists($tempFile)) {
                    // Serve the file with proper headers for PDF viewer toolbar
                    return $this->response
                                ->setHeader('Content-Type', 'application/pdf')
                                ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '.pdf"')
                                ->setHeader('Accept-Ranges', 'bytes')
                                ->setBody(file_get_contents($tempFile));
                } else {
                    throw new \Exception('Download failed');
                }
            } catch (\Exception $e) {
                log_message('error', 'Google Drive civil service certificate serve error: ' . $e->getMessage());
                    
                // Fallback: redirect to Google Drive preview URL
                $previewUrl = "https://drive.google.com/file/d/{$filename}/preview";
                return redirect()->to($previewUrl);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Google Drive service not available.'
            ])->setStatusCode(500);
        }
    } else {
        // File is stored locally
        $filePath = WRITEPATH . 'uploads/civil_service/' . $filename;

        // File does not exist
        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404)
                                  ->setJSON([
                                      'status' => 'warning',
                                      'message' => 'No civil service certificate has been uploaded for this record.'
                                  ]);
        }

        // File exists → stream PDF inline with proper headers for toolbar
        return $this->response
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                    ->setHeader('Accept-Ranges', 'bytes')
                    ->setBody(file_get_contents($filePath));
    }
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
            } catch (\Exception $e) {
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

/**
 * View combined training certificates for document type 7
 * Downloads PDFs from Google Drive and combines them using FPDI
 */
public function viewCombinedTrainingCertificates()
{
    $session = session();

    if (!$session->get('logged_in')) {
        return $this->response->setStatusCode(401)
                              ->setJSON(['message' => 'Unauthorized']);
    }

    $userId = $session->get('user_id');

    $trainingModel = new \App\Models\ApplicantTrainingModel();

    // Get all training certificates for this user
    $certificates = $trainingModel
        ->where('user_id', $userId)
        ->where('certificate_file IS NOT NULL')
        ->where('certificate_file !=', '')
        ->orderBy('added_date', 'ASC')
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
            ->setHeader('Content-Disposition', 'inline; filename="Training_Certificates.pdf"')
            ->setBody($pdf->Output('S'));
    }

    // Use TrainingCertificateCombiner library to combine certificates
    require_once APPPATH . 'Libraries/TrainingCertificateCombiner.php';
    $combiner = new \App\Libraries\TrainingCertificateCombiner();
    
    // Generate unique filename based on user ID and timestamp
    $outputFilename = 'combined_training_user_' . $userId . '_' . time() . '.pdf';
    
    // Before combining, download Google Drive files to local storage temporarily
    $googleDriveService = new \App\Libraries\GoogleDriveOAuthService();
    
    foreach ($certificates as &$cert) {
        $certificateFile = $cert['certificate_file'];
        
        // Check if it's a Google Drive file ID
        $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $certificateFile) && !preg_match('/^\d{10}_/', $certificateFile);
        
        if ($isGoogleDriveFile) {
            log_message('debug', 'Downloading Google Drive file: ' . $certificateFile);
            
            // Download from Google Drive to local temp storage
            $localFilePath = WRITEPATH . 'uploads/trainings/' . $certificateFile . '.pdf';
            
            if (!file_exists($localFilePath)) {
                try {
                    $googleDriveService->downloadFile($certificateFile, $localFilePath);
                    log_message('debug', 'Downloaded to: ' . $localFilePath);
                } catch (\Exception $e) {
                    log_message('error', 'Failed to download Google Drive file: ' . $certificateFile . ' - ' . $e->getMessage());
                }
            }
            
            // Update certificate_file path to local file for combiner
            $cert['certificate_file'] = $certificateFile . '.pdf';
        }
    }
    
    // Combine all certificates into one PDF
    $result = $combiner->combineCertificates($certificates, $outputFilename);
    
    if ($result) {
        // Serve the combined PDF
        $filePath = WRITEPATH . 'uploads/trainings/' . $result;
        
        if (file_exists($filePath)) {
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="All_Training_Certificates.pdf"')
                ->setBody(file_get_contents($filePath));
        }
    }
    
    // If combination failed, return error PDF
    $pdf = new \setasign\Fpdi\Fpdi();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Error Combining Certificates', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Ln(10);
    $pdf->MultiCell(0, 10, 'Unable to combine your training certificates. Please try again or contact support.', 0, 'C');
    
    return $this->response
        ->setHeader('Content-Type', 'application/pdf')
        ->setHeader('Content-Disposition', 'inline; filename="Error_Combining_Certificates.pdf"')
        ->setBody($pdf->Output('S'));
}

/**
 * View multiple training certificates in sequence (actual files, NOT combined)
 * Returns JSON list of certificates for modal viewing
 */
public function viewMultipleTrainingCertificates()
{
    $session = session();

    if (!$session->get('logged_in')) {
       return $this->response->setStatusCode(401)
                              ->setJSON(['message' => 'Unauthorized']);
    }

    $userId = $session->get('user_id');

    $trainingModel = new \App\Models\ApplicantTrainingModel();

    // Get all training certificates for this user
    $certificates = $trainingModel
        ->where('user_id', $userId)
        ->where('certificate_file IS NOT NULL')
        ->where('certificate_file !=', '')
        ->orderBy('added_date', 'ASC')
        ->findAll();

    if (empty($certificates)) {
       return $this->response
            ->setStatusCode(404)
            ->setJSON([
                'status' => 'error',
                'message' => 'No training certificates found.'
            ]);
    }

    // Collect all certificate files
    $certificateFiles = [];
    foreach ($certificates as $cert) {
        if (!empty($cert['certificate_file'])) {
            $certificateFiles[] = [
                'file' => $cert['certificate_file'],
                'training_name' => $cert['training_name'] ?? 'Training',
                'date_from' => !empty($cert['date_from']) ? date('F d, Y', strtotime($cert['date_from'])) : '-',
                'date_to' => !empty($cert['date_to']) ? date('F d, Y', strtotime($cert['date_to'])) : '-',
                'facilitator' => $cert['training_facilitator'] ?? '-',
                'hours' => $cert['training_hours'] ?? '-',
            ];
        }
    }

    if (empty($certificateFiles)) {
       return $this->response
            ->setStatusCode(404)
            ->setJSON([
                'status' => 'warning',
                'message' => 'No training certificates found.'
            ]);
    }

    // Return list of certificate files for the modal to display
   return $this->response
        ->setHeader('Content-Type', 'application/json')
        ->setJSON([
            'status' => 'success',
            'certificates' => $certificateFiles,
            'count' => count($certificateFiles)
        ]);
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

        // Upload to Google Drive ONLY (no local storage)
        try {
            // Initialize Google Drive service
            $driveService = new \App\Libraries\GoogleDriveOAuthService();
            
            if (!$driveService->isAuthenticated()) {
                log_message('warning', 'Google Drive not authenticated for user ID: ' . $userId);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Google Drive authentication required. Please connect your Google Drive account first.',
                    'auth_required' => true,
                    'auth_url' => site_url('google/drive')
                ])->setStatusCode(401);
            }

            // Create temporary file
            $tempDir = WRITEPATH . 'temp/';
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            // Use consistent naming: {timestamp}_{original_name}
            $extension = $file->getClientExtension();
            $baseName = pathinfo($file->getClientName(), PATHINFO_FILENAME);
            // Sanitize filename: remove special characters but keep underscores
            $sanitizedBaseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
            $googleDriveFileName = time() . '_' . $sanitizedBaseName . '.' . $extension;
            
            $tempPath = $tempDir . $googleDriveFileName;
            $fileContent = file_get_contents($file->getTempName());
            file_put_contents($tempPath, $fileContent);
            
            log_message('debug', 'Uploading training certificate to Google Drive: ' . $googleDriveFileName);
            
            // Upload to Google Drive
            $googleFileId = $driveService->uploadFile($tempPath, $googleDriveFileName, $file->getMimeType());
            
            // Clean up temp file
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            
            $fileName = $googleFileId;
            log_message('info', 'Training certificate uploaded to Google Drive. File ID: ' . $googleFileId);
            
        } catch (\Exception $e) {
            log_message('error', 'Training certificate upload failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to upload certificate to Google Drive: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
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

    // Handle file upload - Upload to Google Drive ONLY (no local storage)
    $fileName = $training['certificate_file'];
    $file = $this->request->getFile('training_certificate_file');

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

        // Initialize Google Drive service
        $driveService = new \App\Libraries\GoogleDriveOAuthService();
        
        if (!$driveService->isAuthenticated()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Google Drive service not authenticated. Please contact administrator.'
            ])->setStatusCode(503);
        }

        try {
            // Create temporary file
            $tempDir = WRITEPATH . 'temp/';
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            // Use consistent naming: {timestamp}_{original_name}
            $extension = $file->getClientExtension();
            $baseName = pathinfo($file->getClientName(), PATHINFO_FILENAME);
            $sanitizedBaseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
            $googleDriveFileName = time() . '_' . $sanitizedBaseName . '.' . $extension;
            
            $tempPath = $tempDir . $googleDriveFileName;
            $fileContent = file_get_contents($file->getTempName());
            file_put_contents($tempPath, $fileContent);
            
            log_message('debug', 'Uploading training certificate to Google Drive: ' . $googleDriveFileName);
            
            // Upload to Google Drive
            $googleFileId = $driveService->uploadFile($tempPath, $googleDriveFileName, $file->getMimeType());
            
            // Clean up temp file
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            
            // Delete old file from Google Drive if exists
            if (!empty($training['certificate_file'])) {
                try {
                    $driveService->deleteFile($training['certificate_file']);
                    log_message('info', 'Old training certificate deleted from Google Drive');
                } catch (\Exception $e) {
                    log_message('warning', 'Could not delete old file: ' . $e->getMessage());
                }
            }
            
            $fileName = $googleFileId;
            log_message('info', 'Training certificate uploaded to Google Drive. File ID: ' . $googleFileId);
            
        } catch (\Exception $e) {
            log_message('error', 'Training certificate upload failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to upload certificate to Google Drive: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
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
                
                // Check if it's a Google Drive file ID
                $certificateFileStr = (string) $certificateFile;
                $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $certificateFileStr) 
                                     && !preg_match('/^\d{10}_/', $certificateFileStr);
                
                if ($isGoogleDriveFile) {
                    // Delete from Google Drive
                    try {
                        $driveService = new \App\Libraries\GoogleDriveOAuthService();
                        
                        if ($driveService->isAuthenticated()) {
                            $driveService->deleteFile($certificateFile);
                            log_message('info', 'Training certificate deleted from Google Drive: ' . $certificateFile);
                        } else {
                            log_message('warning', 'Google Drive not authenticated, could not delete file: ' . $certificateFile);
                        }
                    } catch (\Exception $e) {
                        log_message('error', 'Failed to delete training certificate from Google Drive: ' . $e->getMessage());
                        // Continue with deletion even if GD delete fails
                    }
                } else {
                    // Delete local file (legacy support)
                    $filePath = WRITEPATH . 'uploads/trainings/' . $certificateFile;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                        log_message('info', 'Training certificate deleted from local storage: ' . $certificateFile);
                    }
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
    // No filename provided
    if (!$filename) {
        return $this->response->setJSON([
            'status'  => 'warning',
            'message' => 'No file specified.'
        ])->setStatusCode(200);
    }
    
    $filename = urldecode($filename);
    
    // Check if the filename is a Google Drive file ID (typically 28-33 characters long)
    // Local uploaded files have timestamp prefixes like "1772469100_filename.pdf"
    $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $filename) && !preg_match('/^\d{10}_/', $filename);
    
    if ($isGoogleDriveFile) {
        log_message('debug', 'Attempting to view Google Drive file: ' . $filename);
        
        // Handle Google Drive file
        try {
            $driveService = new \App\Libraries\GoogleDriveOAuthService();
            
            log_message('debug', 'Google Drive service initialized, attempting download...');
            
            // Download file content from Google Drive
            $tempPath = sys_get_temp_dir() . '/' . uniqid('gdrive_') . '.pdf';
            
            try {
                $downloadResult = $driveService->downloadFile($filename, $tempPath);
                
                if ($downloadResult && file_exists($tempPath)) {
                    $content = file_get_contents($tempPath);
                    unlink($tempPath); // Clean up temp file
                    
                    log_message('info', 'Successfully downloaded Google Drive file: ' . $filename);
                    
                    return $this->response
                        ->setHeader('Content-Type', 'application/pdf')
                        ->setHeader('Content-Disposition', 'inline; filename="'.$filename.'.pdf"')
                        ->setHeader('Accept-Ranges', 'bytes')
                        ->setBody($content);
                } else {
                    log_message('error', 'Failed to download file from Google Drive: ' . $filename);
                    throw new \Exception('Failed to download file from Google Drive');
                }
            } catch (\Exception $e) {
                log_message('error', 'Google Drive download error: ' . $e->getMessage());
                log_message('error', 'Stack trace: ' . $e->getTraceAsString());
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to download file: ' . $e->getMessage()
                ])->setStatusCode(500);
            }
        } catch (\Exception $e) {
            log_message('error', 'Google Drive service initialization error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Google Drive service error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    } else {
        log_message('debug', 'Viewing local file: ' . $filename);
        
        // Handle local file (fallback for existing files)
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
            log_message('warning', 'Local file not found: ' . $filename);
            return $this->response->setJSON([
                'status'  => 'warning',
                'message' => 'No file has been uploaded for this document.'
            ])->setStatusCode(200);
        }

        return $this->response
            ->setHeader('Content-Type', mime_content_type($path))
            ->setHeader('Content-Disposition', 'inline; filename="'.$filename.'"')
            ->setHeader('Accept-Ranges', 'bytes')
            ->setBody(file_get_contents($path));
    }
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

    // Initialize Google Drive service using OAuth
    $driveService = new \App\Libraries\GoogleDriveOAuthService();
    
    // Log authentication status for debugging
    log_message('debug', 'Google Drive OAuth enabled: ' . ($driveService->isEnabled() ? 'YES' : 'NO'));
    
    if (!$driveService->isEnabled()) {
        log_message('warning', 'Google Drive not authenticated for user ID: ' . $userId);
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Google Drive authentication required. Please connect your Google Drive account first.',
            'auth_required' => true,
            'auth_url' => site_url('google/drive')
        ])->setStatusCode(401);
    }
    
    // Use Google Drive for file storage - NO LOCAL FALLBACK
    try {
        // Create temporary file with the content
        $tempDir = WRITEPATH . 'temp/';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        // Generate a consistent filename: {timestamp}_{original_name}
        $extension = $file->getClientExtension();
        $baseName = pathinfo($file->getClientName(), PATHINFO_FILENAME);
        // Sanitize filename: remove special characters but keep underscores
        $sanitizedBaseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
        $googleDriveFileName = time() . '_' . $sanitizedBaseName . '.' . $extension;
        
        $tempFileName = $googleDriveFileName;
        $tempPath = $tempDir . $tempFileName;
        
        // Get the file content and write it directly
        $fileContent = file_get_contents($file->getTempName());
        file_put_contents($tempPath, $fileContent);
        
        log_message('debug', 'Uploading file to Google Drive: ' . $googleDriveFileName);
        
        // Upload to Google Drive with consistent naming
        $driveFileId = $driveService->uploadFile(
            $tempPath,
            $googleDriveFileName,
            $file->getMimeType()
        );
        
        // Clean up temporary file
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }
        
        log_message('info', 'File uploaded to Google Drive successfully. File ID: ' . $driveFileId);
        
        // Save Google Drive file ID to database instead of local filename
        $result = $fileModel->saveDocument($userId, $documentTypeId, $driveFileId);
        
        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => $documentType['document_type_name'].' updated successfully!',
                'file_id' => $driveFileId,
                'file_url' => $driveService->getFileUrl($driveFileId)
            ]);
        } else {
            log_message('error', 'Failed to save document record in database');
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to save document in database.'
            ]);
        }
    } catch (\Exception $e) {
        log_message('error', 'Google Drive upload error: ' . $e->getMessage());
        log_message('debug', 'Exception trace: ' . $e->getTraceAsString());
        
        // Clean up temp file if it exists
        if (isset($tempPath) && file_exists($tempPath)) {
            unlink($tempPath);
        }
        
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to upload to Google Drive: ' . $e->getMessage()
        ])->setStatusCode(500);
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

    // Check if file is stored in Google Drive (by checking if filename looks like a drive ID)
    $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $document['filename']);
    
    if ($isGoogleDriveFile) {
        // Delete from Google Drive
        $driveService = new \App\Libraries\GoogleDriveOAuthService();
        
        if ($driveService->isEnabled()) {
            try {
                $driveService->deleteFile($document['filename']);
            } catch (\Exception $e) {
                // Log the error but continue with DB deletion
                log_message('error', 'Could not delete file from Google Drive: ' . $e->getMessage());
            }
        }
    } else {
        // Delete local file
        $filePath = WRITEPATH . 'uploads/files/' . $document['filename'];
        if (is_file($filePath)) {
            unlink($filePath);
        }
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

/**
 * Get profile photo for display (supports Google Drive and local storage)
 */
public function getProfilePhoto()
{
    $session = session();
    
    // Authentication check
    if (!$session->get('logged_in')) {
        return $this->response->setStatusCode(401);
    }
    
    $userId = $session->get('user_id');
    
    $applicantModel = new ApplicantModel();
    $profile = $applicantModel->where('user_id', $userId)->first();
    
    if (empty($profile['photo'])) {
        return redirect()->to(base_url('public/assets/images/default-profile.png'));
    }
    
    $photoIdentifier = $profile['photo'];
    
    // Check if it's a Google Drive file ID
    $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $photoIdentifier) && 
                         !preg_match('/^\d{10}_/', $photoIdentifier);
    
    if ($isGoogleDriveFile) {
        // Serve from Google Drive
        try {
            $driveService = new \App\Libraries\GoogleDriveOAuthService();
            
            if (!$driveService->isEnabled()) {
                // Fallback to default if service not available
                return redirect()->to(base_url('public/assets/images/default-profile.png'));
            }
            
            // Get file content from Google Drive
            $tempPath = WRITEPATH . 'temp/profile_photo_' . $userId . '.jpg';
            
            // Download file temporarily
            $driveService->downloadFile($photoIdentifier, $tempPath);
            
            // Read and output file
            if (file_exists($tempPath)) {
                $mimeType = mime_content_type($tempPath);
                $content = file_get_contents($tempPath);
                
                // Clean up temp file
                unlink($tempPath);
                
                return $this->response
                    ->setContentType($mimeType)
                    ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->setHeader('Pragma', 'no-cache')
                    ->setHeader('Expires', '0')
                    ->setBody($content);
            } else {
                return redirect()->to(base_url('public/assets/images/default-profile.png'));
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error serving profile photo from Google Drive: ' . $e->getMessage());
            return redirect()->to(base_url('public/assets/images/default-profile.png'));
        }
    } else {
        // Local file - serve directly
        $photoPath = FCPATH . 'uploads/' . $photoIdentifier;
        
        if (file_exists($photoPath)) {
            $mimeType = mime_content_type($photoPath);
            return $this->response
                ->setContentType($mimeType)
                ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->setHeader('Pragma', 'no-cache')
                ->setHeader('Expires', '0')
                ->setBody(file_get_contents($photoPath));
        } else {
            return redirect()->to(base_url('public/assets/images/default-profile.png'));
        }
    }
}

}
