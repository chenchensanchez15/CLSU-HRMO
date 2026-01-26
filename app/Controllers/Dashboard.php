<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\JobApplicationModel;
use App\Models\JobVacancyModel;
use App\Models\ApplicantModel;

class Dashboard extends BaseController
{  public function index()
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
$builder = $db->table('job_applications');
$builder->select('
    job_applications.id_job_application,
    job_applications.job_vacancy_id,
    job_vacancies.position_title,
    job_vacancies.office AS department,
    job_vacancies.plantilla_item_no,
    job_vacancies.salary_grade,
    job_vacancies.monthly_salary,
    job_vacancies.posted_at AS posting_date,
    job_vacancies.application_deadline AS closing_date,
    job_applications.application_status
');
$builder->join('job_vacancies', 'job_vacancies.id = job_applications.job_vacancy_id', 'left');
$builder->where('job_applications.user_id', $userId);
$applications = $builder->get()->getResultArray();

    // If no applications, make sure $applications is an empty array
    if (!$applications) {
        $applications = [];
    }

    // List of job_vacancy_id that the user already applied to
    $appliedJobIds = array_column($applications, 'job_vacancy_id');

    // Fetch all posted job vacancies
    $vacancyModel = new JobVacancyModel();
    $vacancies = $vacancyModel
        ->where('is_posted', 1)
        ->orderBy('posted_at', 'DESC')
        ->findAll();

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
        'vacancies' => $vacancies,
        'profile' => $profile,
        'profilePhoto' => $profilePhoto,
        'appliedJobIds' => $appliedJobIds
    ];

    return view('dashboard', $data);
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
}
