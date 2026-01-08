<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // TEMP USER (Juan Dela Cruz)
        $userId = 3;

        // USER INFO
        $user = $db->table('users')
            ->select('first_name, last_name, email')
            ->where('id', $userId)
            ->get()
            ->getRowArray();

        // SAFETY FALLBACK
        if (!$user) {
            $user = [
                'first_name' => 'Guest',
                'last_name'  => 'User',
                'email'      => 'guest@example.com'
            ];
        }

        // USER APPLICATIONS
            $applications = $db->table('applications')
            ->select('job_positions.position_title, job_positions.department, applications.status')
            ->join('job_positions', 'job_positions.id = applications.vacancy_id')
            ->where('applications.user_id', $userId)
            ->get()
            ->getResultArray();


        // 👉 GET VACANT JOBS FROM job_positions
        $vacancies = $db->table('job_positions')
            ->where('status', 'Open')
            ->orderBy('application_deadline', 'ASC')
            ->get()
            ->getResultArray();

        return view('dashboard', [
            'user' => $user,
            'applications' => $applications,
            'vacancies' => $vacancies
        ]);
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
