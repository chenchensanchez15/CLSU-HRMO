<?php

namespace App\Controllers;

class Apply extends BaseController
{
    public function index($vacancyId)
    {
        $db = \Config\Database::connect();

        // TEMP USER (Juan Dela Cruz)
        $userId = 3;

        // Get job details
        $job = $db->table('job_positions')
            ->where('id', $vacancyId)
            ->get()
            ->getRowArray();

        if (!$job) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Job not found');
        }

        // Check if already applied
        $alreadyApplied = $db->table('applications')
            ->where([
                'user_id' => $userId,
                'vacancy_id' => $vacancyId
            ])
            ->get()
            ->getRow();

        return view('apply', [
            'job' => $job,
            'alreadyApplied' => $alreadyApplied
        ]);
    }
}
