<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException; // make sure this is at the top

use App\Controllers\BaseController;
use App\Models\JobApplicationModel;
use App\Models\JobVacancyModel;
use App\Models\PlantillaItemModel;
use App\Models\JobPublicationModel;
use App\Models\ApplicantModel;
use App\Models\JobPublicationRequirementsModel;

use App\Models\ApplicantEducationModel;
use App\Models\ApplicantWorkExperienceModel;
use App\Models\ApplicantDocumentsModel; 
use App\Models\ApplicationEducationModel;
use App\Models\ApplicationCivilServiceModel;

class Applications extends BaseController
{
    protected $jobVacancyModel;
    protected $plantillaItemModel;
    protected $jobPublicationModel;
    protected $jobApplications;
    protected $applicantPersonal;
    protected $educationModel;
    protected $workModel;
    protected $documentModel; 
    protected $civilServiceModel;
    protected $jobPublicationRequirementsModel; 

    public function __construct()
    {
        $this->jobVacancyModel = new JobVacancyModel();          
        $this->plantillaItemModel = new PlantillaItemModel();          
        $this->jobPublicationModel = new JobPublicationModel();          
        $this->jobApplications = new JobApplicationModel();   
        $this->applicantPersonal = new ApplicantModel();      
        // Family background functionality removed
        $this->educationModel = new ApplicantEducationModel();
        $this->workModel = new ApplicantWorkExperienceModel();
        $this->documentModel = new ApplicantDocumentsModel(); 
        $this->civilServiceModel = new ApplicationCivilServiceModel();
        $this->jobPublicationRequirementsModel = new JobPublicationRequirementsModel();
    }
    
    public function apply($id = null)
{
    if (!$id) return redirect()->to('/jobs');

    // Get job vacancy with related details
    $builder = $this->jobVacancyModel->db->table('job_vacancies jv');
    $builder->select([
        'jv.id_vacancy as id',
        'jv.plantilla_item_id',
        'jv.date_posted',
        'jv.created_at',
        'jp.interview_date',
        'jp.interview_venue',
        'jp.publication_status',
        'jp.type as publication_type',
        'jp.hr_head',
        'jp.application_deadline',
        'jp.remarks',
        'pi.item_number as plantilla_item_no',
        'pi.xItemTitle as position_title',
        'pi.ItemSalaryGrade as salary_grade',
        'pos.position_name',
        'o.office_name',
        'd.division_name as department',
        'pi.ItemStatus as status',

    ]);
    $builder->join('job_publications jp', 'jv.publication_id = jp.id_publication', 'left');
    $builder->join('`hrmis-template`.plantilla_items pi', 'jv.plantilla_item_id = pi.id_plantilla_item', 'left');
    $builder->join('`hrmis-template`.lib_positions pos', 'pi.position_id = pos.id_position', 'left');
    $builder->join('`hrmis-template`.lib_offices o', 'pi.item_area_code = o.office_code', 'left');
    $builder->join('`hrmis-template`.lib_divisions d', 'o.id_office = d.office_id', 'left');
    $builder->where('jv.id_vacancy', $id);
    $builder->where('jp.publication_status', 1); // Only show published jobs
    
    $job = $builder->get()->getRowArray();
    
    if (!$job) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Job not found');

    // Add monthly salary calculation
    $job['monthly_salary'] = $this->get_monthly_salary($job['id']);

    $user_id = session()->get('user_id');
    if (!$user_id) {
        // Store the job ID in session for redirect after login
        session()->set('redirect_after_login', '/applications/apply/' . $id);
        return redirect()->to('/login');
    }

    // Check if user was created by admin (has pre-filled data)
    $createdBy = session()->get('created_by') ?? 0;
    
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
    
    // Ensure profile is not null, provide default values if needed
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
    
    // Get position-specific document requirements
    $requirements = $this->jobPublicationRequirementsModel->getRequirementsByVacancy($id);
    
    // Fetch user's uploaded documents from applicant_documents
    $userDocs = $db->table('applicant_documents')
        ->where('user_id', $user_id)
        ->get()
        ->getResultArray();
    
    // Map documents by type for easy access in view
    $documentsMap = [];
    foreach ($userDocs as $doc) {
        $docTypeId = $doc['document_type_id'];
        $filename = $doc['filename'];
        
        // Map document types to field names used in apply form
        if ($docTypeId == 1) $documentsMap['pds'] = $filename;              // PDS
        if ($docTypeId == 2) $documentsMap['performance_rating'] = $filename; // Performance Rating
        if ($docTypeId == 3) $documentsMap['eligibility'] = $filename;      // Eligibility/Rating/License
        if ($docTypeId == 4) $documentsMap['tor'] = $filename;              // TOR
        if ($docTypeId == 5) $documentsMap['diploma'] = $filename;          // Diploma
        if ($docTypeId == 6) $documentsMap['employment'] = $filename;       // Certificate of Employment
        if ($docTypeId == 7) $documentsMap['trainings'] = $filename;        // Trainings
    }

    return view('apply', [
        'job'         => $job,
        'profile'     => $profile,
        'requirements'=> $requirements,
        'createdBy'   => $createdBy,
        'documents'   => $documentsMap
    ]);
}

public function submit($id = null)
{
    // Get job vacancy with related details
    $builder = $this->jobVacancyModel->db->table('job_vacancies jv');
    $builder->select([
        'jv.id_vacancy as id',
        'jv.plantilla_item_id',
        'jv.date_posted',
        'jv.created_at',
        'jp.interview_date',
        'jp.interview_venue',
        'jp.publication_status',
        'jp.type as publication_type',
        'jp.hr_head',
        'jp.application_deadline',
        'jp.remarks',
        'pi.item_number as plantilla_item_no',
        'pi.xItemTitle as position_title',
        'pi.ItemSalaryGrade as salary_grade',
        'pos.position_name',
        'o.office_name',
        'd.division_name as department',
        'pi.ItemStatus as status',

    ]);
    $builder->join('job_publications jp', 'jv.publication_id = jp.id_publication', 'left');
    $builder->join('`hrmis-template`.plantilla_items pi', 'jv.plantilla_item_id = pi.id_plantilla_item', 'left');
    $builder->join('`hrmis-template`.lib_positions pos', 'pi.position_id = pos.id_position', 'left');
    $builder->join('`hrmis-template`.lib_offices o', 'pi.item_area_code = o.office_code', 'left');
    $builder->join('`hrmis-template`.lib_divisions d', 'o.id_office = d.office_id', 'left');
    $builder->where('jv.id_vacancy', $id);
    $builder->where('jp.publication_status', 1); // Only show published jobs
    
    $job = $builder->get()->getRowArray();
    
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
        'job_vacancy_id' => $id, // Use the original vacancy ID directly
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
        'clsu_employee_type' => $this->request->getPost('clsu_employee_type') ?? null,
        'clsu_employee_specify' => $this->request->getPost('clsu_employee_specify') ?? null,
        'religion' => $this->request->getPost('religion') ?? null,
        'is_indigenous' => $this->request->getPost('is_indigenous') ?? 'No',
        'indigenous_specify' => $this->request->getPost('indigenous_specify') ?? null,
        'is_pwd' => $this->request->getPost('is_pwd') ?? 'No',
        'pwd_type' => $this->request->getPost('pwd_type') ?? null,
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
// Get civil service records from applicant_civil_service, excluding deleted ones
$user_id = session()->get('user_id');

// Get deleted record IDs from form submission
$deletedIds = $this->request->getPost('deleted_civil_service') ?? [];

$civilQuery = $db->table('applicant_civil_service')
                 ->where('user_id', $user_id)
                 ->orderBy('date_of_exam', 'DESC');

// Exclude deleted records if any
if (!empty($deletedIds)) {
    $civilQuery->whereNotIn('id', $deletedIds);
}

$civilServiceRecords = $civilQuery->get()->getResultArray();

foreach ($civilServiceRecords as $cs) {
    // Copy all data exactly as-is (like trainings)
    $certificateName = $cs['certificate'] ?? null;
    
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
        // Use consistent naming: {timestamp}_{original_name}
        $extension = $uploadedFiles[$i]->getClientExtension();
        $baseName = pathinfo($uploadedFiles[$i]->getClientName(), PATHINFO_FILENAME);
        // Sanitize filename: remove special characters but keep underscores
        $sanitizedBaseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
        $certificateFile = time() . '_' . $sanitizedBaseName . '.' . $extension;
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
// DOCUMENTS: Now handled via applicant_documents (per-user, not per-application)
// Documents are uploaded via profile/account and linked by user_id
// =========================

    return $this->response->setJSON([
        'success' => true,
        'application_id' => $application_id
    ]);
}

public function view($application_id = null)
{
    // 🔒 Authentication check - Users must be logged in to view applications
    $session = session();
    if (!$session->get('logged_in')) {
        return redirect()->to('/login');
    }
    
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
                    'level_name' => $index === 0 ? $levelName : '', // Show level name only on first row,
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
                'level_name' => $levelName,
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
    // Fetch trainings (BEFORE documents section so it's available for combination)
    // -------------------------
    $trainings = [];
    $user_id = $profile['user_id'] ?? $app['user_id'] ?? null;
   if ($user_id) {
        $trainings = $db->table('application_trainings at')
                        ->join('lib_training_category tc', 'at.training_category_id = tc.id_training_category', 'left')
                        ->select('at.id_application_trainings, at.training_name, at.date_from, at.date_to, at.training_facilitator, at.training_hours, at.training_sponsor, at.training_remarks, at.certificate_file, tc.training_category_name')
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
    // Fetch uploaded documents - From applicant_documents table + Google Drive metadata
    // -------------------------
    $userId = $app['user_id'] ?? null;
    $googleDriveFiles = [];
    
    log_message('debug', '=== START: Fetching documents for application view ===');
    log_message('debug', 'User ID from application: ' . ($userId ?? 'NULL'));
    log_message('debug', 'Application ID: ' . ($app['id_job_application'] ?? 'NULL'));
    log_message('debug', 'Trainings count available: ' . count($trainings));
    
    if ($userId) {
        log_message('debug', 'Fetching documents from applicant_documents for user ID: ' . $userId);
        
        // Get documents from applicant_documents table
        $docs = $db->table('applicant_documents')
                   ->where('user_id', $userId)
                   ->get()
                   ->getResultArray();
        
        log_message('debug', 'SQL Query: SELECT * FROM applicant_documents WHERE user_id = ' . $userId);
        log_message('debug', 'Found ' . count($docs) . ' documents in database');
        
        if (empty($docs)) {
            log_message('warning', 'NO DOCUMENTS FOUND in applicant_documents for user ' . $userId);
        } else {
            // Log all document records
            foreach ($docs as $doc) {
                log_message('debug', '  DB Record: id=' . $doc['id'] . ', type=' . $doc['document_type_id'] . ', filename=' . $doc['filename']);
            }
        }
        
        // For each document, get Google Drive file metadata
        try {
            $driveService = new \App\Libraries\GoogleDriveOAuthService();
            
            log_message('debug', 'Google Drive service initialized');
            log_message('debug', 'Is Enabled: ' . ($driveService->isEnabled() ? 'YES' : 'NO'));
            
            if ($driveService->isEnabled()) {
                log_message('debug', 'Google Drive service is enabled');
                
                $client = $driveService->getClient();
                $token = $client->getAccessToken();
                
                // Handle both string and array tokens
                if (is_string($token)) {
                    $token = json_decode($token, true);
                }
                
                $accessToken = $token['access_token'] ?? null;
                
                log_message('debug', 'Access token obtained: ' . (empty($accessToken) ? 'EMPTY' : 'OK'));
                
                foreach ($docs as $doc) {
                    $fileId = $doc['filename']; // This is the Google Drive file ID
                    $docTypeId = $doc['document_type_id'];
                                    
                    // Use document type label as filename (cleaner display)
                    $docLabels = [
                        1 => 'Personal Data Sheet (PDS)',
                        2 => 'Performance Rating',
                        3 => 'Certificate of Eligibility/Rating/License',
                        4 => 'Transcript of Records (TOR)',
                        5 => 'Diploma or Proof of Graduation',
                        6 => 'Certificate of Employment',
                        7 => 'Certificate of Trainings and Seminars'
                    ];
                                    
                    $fileName = $docLabels[$docTypeId] ?? 'Document';
                    
                    // Special handling for trainings (document_type_id = 7)
                    // Combine multiple training certificates into one PDF
                    if ($docTypeId == 7 && !empty($trainings)) {
                        log_message('debug', 'Processing training certificates combination...');
                        log_message('debug', 'Number of trainings found: ' . count($trainings));
                        
                        // Log each training
                        foreach ($trainings as $idx => $training) {
                            log_message('debug', 'Training #' . ($idx + 1) . ': ' . ($training['training_name'] ?? 'N/A') . ', Certificate: ' . ($training['certificate_file'] ?? 'NONE'));
                        }
                        
                        $combiner = new \App\Libraries\TrainingCertificateCombiner();
                        $combinedFile = $combiner->getCombinedCertificatePath($application_id, $trainings);
                        
                        if ($combinedFile) {
                            // Use the combined PDF file path instead
                            $fileId = $combinedFile;
                            log_message('debug', 'Using combined training certificate: ' . $combinedFile);
                            
                            // Verify the file was created
                            $fullPath = WRITEPATH . 'uploads/trainings/' . $combinedFile;
                            if (file_exists($fullPath)) {
                                log_message('debug', 'Combined PDF exists at: ' . $fullPath . ', Size: ' . filesize($fullPath) . ' bytes');
                            } else {
                                log_message('error', 'Combined PDF NOT FOUND at: ' . $fullPath);
                            }
                        } else {
                            log_message('error', 'Failed to generate combined training certificate');
                        }
                    }
                                    
                    // Add file entry with document type label
                    $googleDriveFiles[] = [
                        'id' => $fileId,
                        'name' => $fileName,
                        'document_type_id' => $docTypeId,
                        'mimeType' => 'application/pdf'
                    ];
                                    
                    log_message('debug', '  Added document: ' . $fileName . ' (ID: ' . $fileId . ')');
                }
                                
                log_message('debug', 'Total files after processing: ' . count($googleDriveFiles));
                
                log_message('debug', 'Total files collected: ' . count($googleDriveFiles));
            } else {
                log_message('error', 'Google Drive service NOT ENABLED');
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception fetching Google Drive metadata: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
        }
    } else {
        log_message('error', 'No user_id available for fetching documents');
    }
    
    log_message('debug', 'Final googleDriveFiles count: ' . count($googleDriveFiles));
    
    // Store Google Drive files in app data
    $app['google_drive_files'] = $googleDriveFiles;

    // -------------------------
    // Fetch job details
    // -------------------------
    $jobBuilder = $db->table('job_vacancies jv');
    $jobBuilder->select([
        'pi.xItemTitle as position_title',
        'o.office_name as office',
        'd.division_name as department',
        'jp.application_deadline',
        'pi.item_number as plantilla_item_no',
        'pi.ItemSalaryGrade as salary_grade',
        'pos.position_name',
        'pi.ItemStatus as status'
    ]);
    $jobBuilder->join('job_publications jp', 'jv.publication_id = jp.id_publication', 'left');
    $jobBuilder->join('`hrmis-template`.plantilla_items pi', 'jv.plantilla_item_id = pi.id_plantilla_item', 'left');
    $jobBuilder->join('`hrmis-template`.lib_positions pos', 'pi.position_id = pos.id_position', 'left');
    $jobBuilder->join('`hrmis-template`.lib_offices o', 'pi.item_area_code = o.office_code', 'left');
    $jobBuilder->join('`hrmis-template`.lib_divisions d', 'o.id_office = d.office_id', 'left');
    $jobBuilder->where('jv.id_vacancy', $app['job_vacancy_id']);
    
    $job = $jobBuilder->get()->getRowArray() ?? [
        'position_title' => '-',
        'office'         => '-',
        'department'     => '-',
        'application_deadline' => null,
        'plantilla_item_no' => '-',
        'salary_grade' => '-',
        'position_name' => '-',
        'status' => '-',
        'description' => '-',
        'education' => '-',
        'training' => '-',
        'experience' => '-',
        'eligibility' => '-',
        'competency' => '-',
        'duties_responsibilities' => '-',
        'application_requirements' => '-'
    ];
    
    // Compute monthly salary using the same logic as in Home controller
    $job['monthly_salary'] = $this->get_monthly_salary($app['job_vacancy_id']);

    // -------------------------
    // Fetch applicant profile
    // -------------------------
    $profileModel = new \App\Models\ApplicantModel();
    $profile = $profileModel->where('user_id', $app['user_id'])->first() ?? [];

    // -------------------------
    // Profile photo - Support both Google Drive and local storage
    // -------------------------
    $profilePhoto = null;
    $isGoogleDrivePhoto = false;
    
    if (!empty($profile['photo'])) {
        // Check if it's a Google Drive file ID (alphanumeric, 20+ chars, no timestamp prefix)
        $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $profile['photo']) && !preg_match('/^\d{10}_/', $profile['photo']);
        
        if ($isGoogleDriveFile) {
            log_message('debug', 'Profile has Google Drive photo: ' . $profile['photo']);
            
            // Use the Photo controller endpoint which handles Google Drive authentication properly
            $profilePhoto = site_url('account/getProfilePhoto/' . $userId);
            $isGoogleDrivePhoto = true;
            
            log_message('info', 'Using Photo controller endpoint for profile photo');
        } else {
            // Local file - check if exists
            $photoPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $profile['photo'];
            if (file_exists($photoPath)) {
                $profilePhoto = base_url('uploads/' . $profile['photo']);
            }
        }
    }

    // -------------------------
    // Pass all data to the view
    // -------------------------
    return view('applications/view', [
        'app'                => $app,
        'job'                => $job,
        'profile'            => $profile,
        'profilePhoto'       => $profilePhoto,
        'isGoogleDrivePhoto' => $isGoogleDrivePhoto,
        'trainings'          => $trainings,
        'googleDriveFiles'   => $googleDriveFiles
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
    'clsu_employee_type'     => $this->request->getPost('clsu_employee_type') ?: null,
    'clsu_employee_specify'  => ($this->request->getPost('is_clsu_employee') === 'Yes') ? $this->request->getPost('clsu_employee_specify') ?: '-' : null,
    'religion'               => $this->request->getPost('religion') ?: '-',

    'is_indigenous'          => $this->request->getPost('is_indigenous') ?: 'No',
    'indigenous_specify'     => ($this->request->getPost('is_indigenous') === 'Yes') ? $this->request->getPost('indigenous_specify') ?: '-' : null,

    'is_pwd'                 => $this->request->getPost('is_pwd') ?: 'No',
    'pwd_type'               => $this->request->getPost('pwd_type') ?: null,
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
        'Elementary'         => ['key' => 'elementary', 'id' => 1],
        'Secondary'          => ['key' => 'secondary', 'id' => 2],
        'Vocational / Trade' => ['key' => 'vocational', 'id' => 3],
        'College'            => ['key' => 'college', 'id' => 4],
        'Graduate Studies'   => ['key' => 'graduate', 'id' => 5]
    ];
    $eduTable = $db->table('application_education');

    foreach ($eduLevels as $level => $levelData) {
        $key = $levelData['key'];
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
                             ->where('degree_level_id', $levelData['id'])
                             ->get()
                             ->getRowArray();

        if ($existing) {
            $eduTable->where('id_application_education', $existing['id_application_education'])
                     ->update($data);
        } else {
            $data['job_application_id'] = $job_application_id;
            $data['degree_level_id'] = $levelData['id'];
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

// Get deleted record IDs from form submission
$deletedIds = $this->request->getPost('deleted_civil_service') ?? [];

$civilQuery = $db->table('applicant_civil_service')
                 ->where('user_id', $user_id)
                 ->orderBy('date_of_exam', 'DESC');

// Exclude deleted records if any
if (!empty($deletedIds)) {
    $civilQuery->whereNotIn('id', $deletedIds);
}

$civilServices = $civilQuery->get()->getResultArray();

$civilTable = $db->table('application_civil_service');

// Delete existing records for this application
$civilTable->where('job_application_id', $job_application_id)->delete();

foreach ($civilServices as $cs) {
    // Copy all data exactly as-is (like trainings)
    $certificateName = $cs['certificate'] ?? null;
    
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
        // Use consistent naming: {timestamp}_{original_name}
        $extension = $uploadedFiles[$i]->getClientExtension();
        $baseName = pathinfo($uploadedFiles[$i]->getClientName(), PATHINFO_FILENAME);
        // Sanitize filename: remove special characters but keep underscores
        $sanitizedBaseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
        $certificateFile = time() . '_' . $sanitizedBaseName . '.' . $extension;
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
    // Update Documents - REMOVED
    // Documents are now managed via applicant_documents (per-user) through Account controller
    // -------------------
    return redirect()->back()->with('error', 'Document updates are now handled through your profile page.');
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
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Unauthorized access'
        ])->setStatusCode(403);
    }

    $db = \Config\Database::connect();

    // Get the application to verify ownership
    $application = $db->table('job_applications')
                     ->where('id_job_application', $application_id)
                     ->get()
                     ->getRowArray();

    if (!$application) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Application not found'
        ])->setStatusCode(404);
    }

    // Check if user owns this application
    if ($application['user_id'] != $current_user_id) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Unauthorized access'
        ])->setStatusCode(403);
    }

    // Get document record from applicant_documents by user_id
    $record = $db->table('applicant_documents')
                 ->where('user_id', $current_user_id)
                 ->get()
                 ->getResultArray();
    
    // Convert to format expected by view (map document_type_id to field names)
    if ($record) {
        $docMap = [];
        foreach ($record as $docRecord) {
            $docTypeId = $docRecord['document_type_id'];
            $filename = $docRecord['filename'];
            
            // Map document types to field names
            if ($docTypeId == 1) $docMap['pds'] = $filename;              // PDS
            if ($docTypeId == 2) $docMap['performance_rating'] = $filename; // Performance Rating
            if ($docTypeId == 4) $docMap['tor'] = $filename;              // TOR
            if ($docTypeId == 5) $docMap['diploma'] = $filename;          // Diploma
            if ($docTypeId == 6) $docMap['certificate'] = $filename;      // Certificate of Employment
            if ($docTypeId == 7) $docMap['trainings'] = $filename;        // Trainings
        }
        $record = $docMap;
    }
    
    log_message('debug', "Viewing document: application_id={$application_id}, doc={$doc}");
    log_message('debug', "Record data: " . print_r($record, true));

    if (!$record || empty($record[$doc])) {
        log_message('debug', "No record found or empty document field");
        return $this->response->setJSON([
            'status' => 'warning',
            'message' => 'No file has been uploaded for this document.'
        ])->setStatusCode(404);
    }

    // File path or Google Drive ID
    $file = $record[$doc];
    
    // Check if this is a Google Drive file ID
    $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{28,33}$/', $file) && !preg_match('/^\d{10}_/', $file);
    
    if ($isGoogleDriveFile) {
        // File is stored in Google Drive
        $driveService = new \App\Libraries\GoogleDriveOAuthService();
        
        if ($driveService->isEnabled()) {
            // Redirect to Google Drive public URL
            $publicUrl = $driveService->getFileUrl($file);
            return redirect()->to($publicUrl);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Google Drive service not available.'
            ])->setStatusCode(500);
        }
    } else {
        // File is stored locally (fallback for existing files)
        $filePath = WRITEPATH . 'uploads/files/' . $file;
        
        log_message('debug', "Checking file: {$filePath}");
        log_message('debug', "File exists: " . (file_exists($filePath) ? 'YES' : 'NO'));

        if (!file_exists($filePath)) {
            return $this->response->setJSON([
                'status' => 'warning',
                'message' => 'File does not exist on server.'
            ])->setStatusCode(404);
        }

        // Stream the file inline
    }
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


public function updateFiles()
{
    // Log the request
    log_message('debug', 'updateFiles called');
    log_message('debug', 'POST data: ' . print_r($this->request->getPost(), true));
    log_message('debug', 'FILES data: ' . print_r($this->request->getFiles(), true));
    
    $application_id = $this->request->getPost('job_application_id');
    
    if (!$application_id) {
        log_message('error', 'No application_id provided');
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Application ID is required.'
        ]);
    }
    
    log_message('debug', 'Processing application ID: ' . $application_id);
    
    $db = \Config\Database::connect();
    $uploadPath = WRITEPATH . 'uploads/files/';
    
    // Ensure upload directory exists
    if (!is_dir($uploadPath)) {
        if (!mkdir($uploadPath, 0755, true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Upload directory could not be created.'
            ]);
        }
    }
    
    // Documents are now managed via applicant_documents (per-user) through Account controller
    return $this->response->setJSON([
        'success' => false,
        'message' => 'Document management is now handled through your profile page.'
    ]);
}
public function getFiles($id)
{
    log_message('debug', 'getFiles called with ID: ' . $id);
    
    $db = \Config\Database::connect();
    
    // Get user_id from application
    $application = $db->table('job_applications')
        ->select('user_id')
        ->where('id_job_application', $id)
        ->get()
        ->getRowArray();
    
    if (!$application) {
        return $this->response->setJSON([
            'pds' => null,
            'performance_rating' => null,
            'resume' => null,
            'tor' => null,
            'diploma' => null
        ]);
    }
    
    // Get documents from applicant_documents by user_id
    $docs = $db->table('applicant_documents')
        ->where('user_id', $application['user_id'])
        ->get()
        ->getResultArray();
    
    // Map to expected format
    $files = [];
    foreach ($docs as $doc) {
        $docTypeId = $doc['document_type_id'];
        if ($docTypeId == 1) $files['pds'] = $doc['filename'];
        if ($docTypeId == 2) $files['performance_rating'] = $doc['filename'];
        if ($docTypeId == 4) $files['tor'] = $doc['filename'];
        if ($docTypeId == 5) $files['diploma'] = $doc['filename'];
    }
    
    log_message('debug', 'Files found: ' . print_r($files, true));
    
    if (!$files) {
        return $this->response->setJSON([
            'pds' => null,
            'performance_rating' => null,
            'resume' => null,
            'tor' => null,
            'diploma' => null
        ]);
    }
    
    // Return file URLs for viewing
    return $this->response->setJSON([
        'pds' => $files['pds'] ? base_url('applications/viewDocument/' . $id . '/pds') : null,
        'performance_rating' => $files['performance_rating'] ? base_url('applications/viewDocument/' . $id . '/performance_rating') : null,
        'resume' => $files['resume'] ? base_url('applications/viewDocument/' . $id . '/resume') : null,
        'tor' => $files['tor'] ? base_url('applications/viewDocument/' . $id . '/tor') : null,
        'diploma' => $files['diploma'] ? base_url('applications/viewDocument/' . $id . '/diploma') : null,
    ]);
}

public function viewCivilCertificate($filename = null)
{
    // If no filename provided, return JSON (frontend will show warning)
    if (!$filename) {
        return $this->response->setStatusCode(404)
                              ->setJSON([
                                  'status'  => 'warning',
                                  'message' => 'No civil service certificate has been uploaded for this record.'
                              ]);
    }

    // Decode filename from URL
    $filename = urldecode($filename);

    // Check if it's a Google Drive file ID
    $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $filename) && !preg_match('/^\d{10}_/', $filename);
    
    if ($isGoogleDriveFile) {
        log_message('debug', 'Viewing civil service certificate from Google Drive: ' . $filename);
        
        try {
            $driveService = new \App\Libraries\GoogleDriveOAuthService();
            
            if ($driveService->isEnabled()) {
                // Create temp file path
                $tempPath = sys_get_temp_dir() . '/civil_cert_' . $filename;
                
                // Download file from Google Drive
                $result = $driveService->downloadFile($filename, $tempPath);
                
                if ($result && file_exists($tempPath)) {
                    $mime = mime_content_type($tempPath);
                    
                    return $this->response
                        ->setHeader('Content-Type', $mime)
                        ->setHeader('Content-Disposition', 'inline; filename="certificate.pdf"')
                        ->setBody(file_get_contents($tempPath));
                } else {
                    log_message('error', 'Failed to download civil service certificate from Google Drive');
                }
            } else {
                log_message('error', 'Google Drive service not enabled');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error viewing civil service certificate from Google Drive: ' . $e->getMessage());
        }
        
        return $this->response->setStatusCode(404)
                              ->setJSON([
                                  'status'  => 'warning',
                                  'message' => 'Unable to retrieve civil service certificate.'
                              ]);
    }

    // Local file handling
    $filePath = WRITEPATH . 'uploads/civil_service/' . $filename;

    // If file doesn't exist locally, return JSON warning
    if (!file_exists($filePath)) {
        return $this->response->setStatusCode(404)
                              ->setJSON([
                                  'status'  => 'warning',
                                  'message' => 'No civil service certificate has been uploaded for this record.'
                              ]);
    }

    // Determine mime type
    $mime = mime_content_type($filePath);

    // Stream file inline (PDF, etc.)
    return $this->response
                ->setHeader('Content-Type', $mime)
                ->setHeader('Content-Disposition', 'inline; filename="'.$filename.'"')
                ->setHeader('Accept-Ranges', 'bytes')
                ->setBody(file_get_contents($filePath));
} 

public function viewTrainingCertificate($id, $filename)
{
    // Check if it's a combined PDF file (generated by TrainingCertificateCombiner)
    $isCombinedPdf = strpos($filename, 'combined_training_') === 0;
    
    if ($isCombinedPdf) {
        log_message('debug', 'Viewing combined training certificate PDF: ' . $filename);
        
        // Local combined PDF file handling
        $filePath = WRITEPATH . 'uploads/trainings/' . $filename;
        
        if (!file_exists($filePath)) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => 'warning',
                    'message' => 'Combined training certificate not found.'
                ]);
        }
        
        $mime = mime_content_type($filePath) ?: 'application/pdf';
        
        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="combined_training_certificates.pdf"')
            ->setBody(file_get_contents($filePath));
    }
    
    // Check if it's a Google Drive file ID
    $isGoogleDriveFile = preg_match('/^[a-zA-Z0-9_-]{20,}$/', $filename) && !preg_match('/^\d{10}_/', $filename);
    
    if ($isGoogleDriveFile) {
        log_message('debug', 'Viewing training certificate from Google Drive: ' . $filename);
        
        try {
            $driveService = new \App\Libraries\GoogleDriveOAuthService();
            
            if ($driveService->isEnabled()) {
                // Create temp file path
                $tempPath = sys_get_temp_dir() . '/training_cert_' . $filename;
                
                // Download file from Google Drive
                $result = $driveService->downloadFile($filename, $tempPath);
                
                if ($result && file_exists($tempPath)) {
                    $mime = mime_content_type($tempPath);
                    
                    return $this->response
                        ->setHeader('Content-Type', $mime)
                        ->setHeader('Content-Disposition', 'inline; filename="training_certificate.pdf"')
                        ->setBody(file_get_contents($tempPath));
                } else {
                    log_message('error', 'Failed to download training certificate from Google Drive');
                }
            } else {
                log_message('error', 'Google Drive service not enabled');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error viewing training certificate from Google Drive: ' . $e->getMessage());
        }
        
        return $this->response->setStatusCode(404)
                              ->setJSON([
                                  'status'  => 'warning',
                                  'message' => 'Unable to retrieve training certificate.'
                              ]);
    }

    // Local file handling
    $filename = basename($filename);
    $filePath = WRITEPATH . 'uploads/trainings/' . $filename;

    if (!file_exists($filePath)) {
        return $this->response
            ->setStatusCode(404)
            ->setJSON([
                'status'  => 'warning',
                'message' => 'No training certificate has been uploaded for this record.'
            ]);
    }

    return $this->response
        ->setHeader('Content-Type', 'application/pdf')
        ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
        ->setBody(file_get_contents($filePath));
}

private function get_monthly_salary($job_vacancy_id)
{
    $db = \Config\Database::connect();
    
    // First get the plantilla_item_id from the job_vacancies table
    $vacancy = $db->table('job_vacancies')
                ->select('plantilla_item_id')
                ->where('id_vacancy', $job_vacancy_id)
                ->get()
                ->getRowArray();
    
    if (!$vacancy || !$vacancy['plantilla_item_id']) {
        return null;
    }
    
    $plantilla_item_id = $vacancy['plantilla_item_id'];
    
    // ✅ Use hrmis-template database explicitly
    $schedule = $db->query("SELECT *
        FROM `hrmis-template`.lib_salary_schedules
        WHERE schedule_forpermanent = 1
        AND schedule_effectivity <= CURDATE()
        ORDER BY schedule_effectivity DESC
        LIMIT 1")->getRow();

    if (!$schedule) return null;

    $item = $db->query("SELECT pi.id_plantilla_item, lp.salary_grade
        FROM `hrmis-template`.plantilla_items pi
        LEFT JOIN `hrmis-template`.lib_positions lp 
            ON pi.position_id = lp.id_position
        WHERE pi.id_plantilla_item = ?", [$plantilla_item_id])->getRow();

    if (!$item || !$item->salary_grade) return null;

    $salary = $db->query("SELECT sg_sin1
        FROM `hrmis-template`.lib_salaries
        WHERE salary_grade = ?
        AND salary_schedule_id = ?
        LIMIT 1", [$item->salary_grade, $schedule->id_salary_schedule])->getRow();

    return $salary ? $salary->sg_sin1 : null;
}

}