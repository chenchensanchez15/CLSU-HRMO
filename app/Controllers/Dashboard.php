<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // TEMP: use Juan Dela Cruz
        $userId = 3;

        // Fetch user + applicant profile (left join)
        $user = $db->table('users')
            ->select('users.first_name, users.last_name, users.email, applicants.contact, applicants.photo')
            ->join('applicants', 'applicants.user_id = users.id', 'left')
            ->where('users.id', $userId)
            ->get()
            ->getRowArray();

        // Safety defaults if null
        if (!$user) {
            $user = [
                'first_name' => 'No',
                'last_name'  => 'Name',
                'email'      => 'noemail@example.com',
                'contact'    => 'N/A',
                'photo'      => null
            ];
        }

        // Fetch user's applications
        $applications = $db->table('applications')
            ->select('job_vacancies.position, job_vacancies.department, applications.status')
            ->join('job_vacancies', 'job_vacancies.id = applications.vacancy_id')
            ->where('applications.user_id', $userId)
            ->get()
            ->getResultArray();

        // Fetch open job vacancies
        $vacancies = $db->table('job_vacancies')
            ->where('status', 'Open')
            ->get()
            ->getResultArray();

        return view('dashboard', [
            'user' => $user,
            'applications' => $applications,
            'vacancies' => $vacancies
        ]);
    }
}
