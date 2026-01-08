<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\JobApplicationModel;
use App\Models\JobVacancyModel;

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

        // Fetch applications for this user
        $applicationModel = new JobApplicationModel();
        $applications = $applicationModel->where('user_id', $userId)->findAll();

        // Fetch all job vacancies (from job_positions table)
        $vacancyModel = new JobVacancyModel();
        $vacancies = $vacancyModel->where('status', 'Open')->findAll();

        $data = [
            'user' => $user,
            'applications' => $applications,
            'vacancies' => $vacancies
        ];

        return view('dashboard', $data);
    }

    public function apply()
    {
        $db = \Config\Database::connect();

        // TEMP USER
        $userId = 3;

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
