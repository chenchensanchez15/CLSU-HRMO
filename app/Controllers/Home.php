<?php

namespace App\Controllers;

use App\Models\JobPositionModel;

class Home extends BaseController
{
    public function index(): string
    {
        // Load Job Position model
        $jobModel = new JobPositionModel();

        // Get all OPEN job vacancies
        $data['jobs'] = $jobModel
            ->where('status', 'Open')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Pass data to view
        return view('home', $data);
    }
}
