<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\JobApplicationModel;
use App\Models\JobVacancyModel;
use App\Models\ApplicantModel;

class Dashboard extends BaseController
{  
    public function index()
{
    $session = session();

    if (!$session->get('logged_in')) {
        return redirect()->to('login');
    }

    $userId = $session->get('user_id');

    // Fetch user info
    $userModel = new UserModel();
    $user = $userModel->find($userId);

    // Fetch user's job applications with job vacancy info
    $db = \Config\Database::connect();
    
    // Get page number for applications pagination (from POST data)
    $appPage = $this->request->getPost('app_page') ?? 1;
    $appsPerPage = 5;
    
    // Count total applications for pagination
    $totalApps = $db->table('job_applications')
        ->where('job_applications.user_id', $userId)
        ->countAllResults(false);
    
    // Fetch paginated applications for display
$builder = $db->table('job_applications');
$builder->select([
    'job_applications.id_job_application',
    'job_applications.job_vacancy_id',
    'job_applications.applied_at',
    'job_applications.application_status',
    'pi.xItemTitle as position_title',
    'o.office_name AS department',
    'pi.item_number as plantilla_item_no',
    'pi.ItemSalaryGrade as salary_grade',

    'jv.date_posted',
    'jp.application_deadline',
    'jp.interview_date'   // ✅ ADD THIS
]);

$builder->join('job_vacancies jv', 'jv.id_vacancy = job_applications.job_vacancy_id', 'left');
$builder->join('job_publications jp', 'jv.publication_id = jp.id_publication', 'left');
$builder->join('`hrmis-template`.plantilla_items pi', 'jv.plantilla_item_id = pi.id_plantilla_item', 'left');
$builder->join('`hrmis-template`.lib_offices o', 'pi.item_area_code = o.office_code', 'left');
$builder->where('job_applications.user_id', $userId);
$builder->orderBy('job_applications.applied_at', 'DESC');
$applications = $builder->get($appsPerPage, ($appPage - 1) * $appsPerPage)->getResultArray();

   // Fetch the latest application per vacancy
$allApplicationsRaw = $db->table('job_applications')
    ->select('job_applications.job_vacancy_id, job_applications.application_status, job_applications.updated_at')
    ->where('user_id', $userId)
    ->orderBy('updated_at', 'DESC')
    ->get()
    ->getResultArray();

// Keep only the latest per vacancy
$allApplications = [];
foreach ($allApplicationsRaw as $app) {
    $vacId = $app['job_vacancy_id'];
    if (!isset($allApplications[$vacId])) {
        $allApplications[$vacId] = $app; // first occurrence is latest because of DESC
    }
}

// Calculate total pages for applications
$totalAppPages = ceil($totalApps / $appsPerPage);

    // If no applications, make sure $applications is an empty array
    if (!$applications) {
        $applications = [];
    }

    // List of job_vacancy_id that the user already applied to
    $appliedJobIds = array_column($applications, 'job_vacancy_id');

    // Fetch all posted job vacancies
    $db = \Config\Database::connect();
    $builder = $db->table('job_vacancies jv');
    $builder->select([
        'jv.id_vacancy',
        'jv.plantilla_item_id',
        'jv.date_posted',
        'jv.created_at',
        'jp.interview_date',
        'jp.interview_venue',
        'jp.publication_status',
        'jp.type as publication_type',
        'jp.hr_head',
        'jp.application_deadline',
        'pi.item_number as plantilla_item_no',
        'pi.xItemTitle as position_title',
        'pi.ItemSalaryGrade as salary_grade',
        'pos.position_name',
        'o.office_name',
        'd.division_name as department',
        'pi.ItemStatus as status'
    ]);
    $builder->join('job_publications jp', 'jv.publication_id = jp.id_publication', 'left');
    $builder->join('`hrmis-template`.plantilla_items pi', 'jv.plantilla_item_id = pi.id_plantilla_item', 'left');
    $builder->join('`hrmis-template`.lib_positions pos', 'pi.position_id = pos.id_position', 'left');
    $builder->join('`hrmis-template`.lib_offices o', 'pi.item_area_code = o.office_code', 'left');
    $builder->join('`hrmis-template`.lib_divisions d', 'o.id_office = d.office_id', 'left');
    $builder->where('jp.publication_status', 1); // Assuming 1 means active/public
    $builder->orderBy('jv.created_at', 'DESC');
    
    $vacancies = $builder->get()->getResultArray();
    // ✅ Compute monthly salary for each vacancy
foreach ($vacancies as &$vac) {
    $vac['monthly_salary'] = $this->get_monthly_salary($vac['plantilla_item_id']) ?? 0;
}


    // Fetch applicant profile
    $applicantModel = new ApplicantModel();
    $profile = $applicantModel->where('user_id', $userId)->first();

    // Check if profile photo exists
    $profilePhoto = null;
    if (!empty($profile['photo'])) {
        $photoPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $profile['photo'];
        if (file_exists($photoPath)) {
            $profilePhoto = $profile['photo'];
        }
    }

    $data = [
        'user' => $user,
        'applications' => $applications,
        'allApplications' => $allApplications,
        'vacancies' => $vacancies,
        'profile' => $profile,
        'profilePhoto' => $profilePhoto,
        'appliedJobIds' => $appliedJobIds,
        'appPage' => $appPage,
        'totalApps' => $totalApps,
        'appsPerPage' => $appsPerPage,
        'totalAppPages' => $totalAppPages
    ];

    return view('dashboard', $data);
}
 private function get_monthly_salary($plantilla_item_id)
{
    $db = \Config\Database::connect();

    $schedule = $db->query("
        SELECT *
        FROM `hrmis-template`.lib_salary_schedules
        WHERE schedule_forpermanent = 1
        AND schedule_effectivity <= CURDATE()
        ORDER BY schedule_effectivity DESC
        LIMIT 1
    ")->getRow();

    if (!$schedule) return null;

    $item = $db->query("
        SELECT pi.id_plantilla_item, lp.salary_grade
        FROM `hrmis-template`.plantilla_items pi
        LEFT JOIN `hrmis-template`.lib_positions lp 
            ON pi.position_id = lp.id_position
        WHERE pi.id_plantilla_item = ?
    ", [$plantilla_item_id])->getRow();

    if (!$item || !$item->salary_grade) return null;

    $salary = $db->query("
        SELECT sg_sin1
        FROM `hrmis-template`.lib_salaries
        WHERE salary_grade = ?
        AND salary_schedule_id = ?
        LIMIT 1
    ", [$item->salary_grade, $schedule->id_salary_schedule])->getRow();

    return $salary ? $salary->sg_sin1 : null;
}

    
    public function apply()
    {
        $db = \Config\Database::connect();

        $userId = session()->get('user_id');
        $vacancyId = $this->request->getPost('vacancy_id');

        // Prevent duplicate application
        $exists = $db->table('job_applications')
            ->where([
                'user_id' => $userId,
                'job_vacancy_id' => $vacancyId
            ])
            ->get()
            ->getRow();

        if ($exists) {
            return redirect()->back()->with('error', 'You already applied for this position.');
        }

        $db->table('job_applications')->insert([
            'user_id' => $userId,
            'job_vacancy_id' => $vacancyId,
            'application_status' => 'Pending',
            'applied_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('success', 'Application submitted successfully.');
    }
    
    // AJAX pagination endpoint
    public function pagination()
    {
        $session = session();
        
        if (!$session->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Not logged in'
            ]);
        }
        
        $userId = $session->get('user_id');
        $db = \Config\Database::connect();
        
        // Get page number from POST data
        $appPage = $this->request->getPost('app_page') ?? 1;
        $appsPerPage = 5;
        
        // Validate page number
        $totalApps = $db->table('job_applications')
            ->where('job_applications.user_id', $userId)
            ->countAllResults(false);
        $totalAppPages = ceil($totalApps / $appsPerPage);
        
        if ($appPage < 1 || $appPage > $totalAppPages) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid page number'
            ]);
        }
        
        // Fetch paginated applications
        $builder = $db->table('job_applications');
$builder->select([
    'job_applications.id_job_application',
    'job_applications.job_vacancy_id',
    'job_applications.applied_at',
    'job_applications.application_status',
    'pi.xItemTitle as position_title',
    'o.office_name AS department',
    'pi.item_number as plantilla_item_no',
    'pi.ItemSalaryGrade as salary_grade',

    'jv.date_posted',
    'jp.application_deadline',
    'jp.interview_date'   // ✅ ADD THIS
]);

        $builder->join('job_vacancies jv', 'jv.id_vacancy = job_applications.job_vacancy_id', 'left');
        $builder->join('job_publications jp', 'jv.publication_id = jp.id_publication', 'left');
        $builder->join('`hrmis-template`.plantilla_items pi', 'jv.plantilla_item_id = pi.id_plantilla_item', 'left');
        $builder->join('`hrmis-template`.lib_offices o', 'pi.item_area_code = o.office_code', 'left');
        $builder->where('job_applications.user_id', $userId);
        $builder->orderBy('job_applications.applied_at', 'DESC');
        $applications = $builder->get($appsPerPage, ($appPage - 1) * $appsPerPage)->getResultArray();
        
        if (!$applications) {
            $applications = [];
        }
        
        // Generate HTML for the applications table and pagination
        $startIndex = ($appPage - 1) * $appsPerPage + 1;
        $html = '<div class="overflow-x-auto">';
        $html .= '<table class="w-full table-auto border-collapse text-xs">';
        $html .= '<thead class="bg-gray-50 text-xs">';
        $html .= '<tr>';
        $html .= '<th class="border-b p-2 text-left font-semibold">No.</th>';
        $html .= '<th class="border-b p-2 text-left font-semibold">Position</th>';
        $html .= '<th class="border-b p-2 text-left font-semibold">Office</th>';
        $html .= '<th class="border-b p-2 text-left font-semibold">Date Applied</th>';
        $html .= '<th class="border-b p-2 text-left font-semibold">Interview</th>';
        $html .= '<th class="border-b p-2 text-left font-semibold">Status</th>';
        $html .= '<th class="border-b p-2 text-left font-semibold">Actions</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        if (empty($applications)) {
            $html .= '<tr>';
            $html .= '<td colspan="7" class="p-3 text-gray-500 text-center italic text-xs">No applications found</td>';
            $html .= '</tr>';
        } else {
            foreach($applications as $index => $app) {
                $displayNumber = $startIndex + $index;
                $html .= '<tr class="hover:bg-gray-50 transition-colors">';
                $html .= '<td class="p-2 border-b">' . $displayNumber . '</td>';
                $html .= '<td class="p-2 border-b font-medium">' . esc($app['position_title']) . '</td>';
                $html .= '<td class="p-2 border-b text-gray-600">' . esc($app['department']) . '</td>';
                $html .= '<td class="p-2 border-b">' . (!empty($app['applied_at']) ? date('M d, Y', strtotime($app['applied_at'])) : '-') . '</td>';
             if (($app['application_status'] ?? '') === 'Scheduled for Interview' && !empty($app['interview_date'])) {
    $interviewDate = date('M d, Y', strtotime($app['interview_date']));
} else {
    $interviewDate = '-';
}

$html .= '<td class="p-2 border-b">' . $interviewDate . '</td>';

                // Status badge
                $status = $app['application_status'] ?? 'Submitted';
                $displayText = ($status === 'Submitted. For Evaluation') ? 'Submitted' : $status;
                $statusClasses = [
                    'Submitted' => 'bg-yellow-100 text-yellow-800',
                    'Under Evaluation' => 'bg-blue-100 text-blue-800',
                    'Not qualified' => 'bg-red-100 text-red-800',
                    'Shortlisted' => 'bg-blue-100 text-blue-800',
                    'Scheduled for Interview' => 'bg-purple-100 text-purple-800',
                    'Withdrawn application' => 'bg-gray-100 text-gray-800',
                    'Did not attend interview' => 'bg-red-100 text-red-800',
                    'Interviewed. Awaiting Result' => 'bg-yellow-100 text-yellow-800',
                    'Not selected' => 'bg-red-100 text-red-800',
                    'Job offered' => 'bg-green-100 text-green-800',
                    'Rejected job offer' => 'bg-red-100 text-red-800',
                    'ACCEPTED' => 'bg-green-100 text-green-800',
                ];
                $badgeClass = $statusClasses[$displayText] ?? 'bg-gray-100 text-gray-800';
                
                $html .= '<td class="p-2 border-b">';
                $html .= '<span class="px-2 py-1 rounded-full text-xs font-medium ' . $badgeClass . '">';
                $html .= esc($displayText);
                $html .= '</span>';
                $html .= '</td>';
                
                // Actions
                $html .= '<td class="p-2 border-b">';
                $html .= '<div class="flex gap-1">';
                $html .= '<a href="' . base_url('applications/view/' . $app['id_job_application']) . '" 
                           class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors">';
                $html .= '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                $html .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>';
                $html .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
                $html .= '</svg>';
                $html .= 'View';
                $html .= '</a>';
                
                $html .= '<a href="#" 
                           class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors withdraw-btn"
                           data-id="' . $app['id_job_application'] . '">';
                $html .= '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                $html .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>';
                $html .= '</svg>';
                $html .= 'Withdraw';
                $html .= '</a>';
                $html .= '</div>';
                $html .= '</td>';
                $html .= '</tr>';
            }
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        
        // Add pagination controls
        $html .= '<div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-4">';
        $html .= '<div class="text-xs text-gray-600">';
        $html .= 'Showing ' . ((($appPage - 1) * $appsPerPage) + 1) . ' to ';
        $html .= min($appPage * $appsPerPage, $totalApps) . ' of ';
        $html .= $totalApps . ' application' . ($totalApps != 1 ? 's' : '');
        $html .= '</div>';
        
        $html .= '<div class="flex items-center gap-2" id="applications-pagination">';
        
        // Prev button
        $html .= '<button onclick="loadApplicationsPage(' . ($appPage - 1) . ')" 
                    class="px-3 py-1 text-xs font-medium ' . ($appPage > 1 ? 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 cursor-pointer' : 'text-gray-400 bg-gray-100 border border-gray-200 cursor-not-allowed') . ' rounded transition-colors"' . ($appPage <= 1 ? ' disabled' : '') . '>';
        $html .= 'Prev';
        $html .= '</button>';
        
        // Current page button
        $html .= '<span class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded">' . $appPage . '</span>';
        
        // Next button
        $html .= '<button onclick="loadApplicationsPage(' . ($appPage + 1) . ')" 
                    class="px-3 py-1 text-xs font-medium ' . ($appPage < $totalAppPages ? 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 cursor-pointer' : 'text-gray-400 bg-gray-100 border border-gray-200 cursor-not-allowed') . ' rounded transition-colors"' . ($appPage >= $totalAppPages ? ' disabled' : '') . '>';
        $html .= 'Next';
        $html .= '</button>';
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $this->response->setJSON([
            'success' => true,
            'html' => $html,
            'currentPage' => $appPage,
            'totalPages' => $totalAppPages
        ]);
    }
}
