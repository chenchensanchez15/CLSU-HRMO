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

    // Fetch applications for this user with job info
    $db = \Config\Database::connect();
    $builder = $db->table('job_applications');
    $builder->select('job_applications.id, job_positions.position_title, job_positions.department, job_positions.item_no, job_positions.created_at as posting_date, job_positions.application_deadline as closing_date, job_applications.application_status');
    $builder->join('job_positions', 'job_positions.id = job_applications.job_position_id', 'left');
    $builder->where('job_applications.user_id', $userId);
    $applications = $builder->get()->getResultArray();

    // Fetch all open job vacancies
    $vacancyModel = new JobVacancyModel();
    $vacancies = $vacancyModel->where('status', 'Open')->findAll();

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
        'profilePhoto' => $profilePhoto, // Pass to view
    ];

    return view('dashboard', $data);
}

    public function apply()
    {
        $db = \Config\Database::connect();

        // TEMP USER (you may replace with session user_id)
        $userId = session()->get('user_id') ?? 3;

        $vacancyId = $this->request->getPost('vacancy_id');

        // Prevent duplicate application
        $exists = $db->table('applications')
            ->where([
                'user_id' => $userId,
                'vacancy_id' => $vacancyId
            ])
            ->get()
            ->getRow();

        if ($exists) {
            return redirect()->back()->with('error', 'You already applied for this position.');
        }

        $db->table('applications')->insert([
            'user_id' => $userId,
            'vacancy_id' => $vacancyId,
            'status' => 'Pending',
            'applied_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('success', 'Application submitted successfully.');
    }
}
