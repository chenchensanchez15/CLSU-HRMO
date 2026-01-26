<?php

namespace App\Controllers;
use App\Models\JobPositionModel;

class Jobs extends BaseController
{
    public function index()
    {
        $jobModel = new JobPositionModel();
        $data['jobs'] = $jobModel->where('is_posted', 1)->findAll();
        return view('home', $data); // This is your job listing page
    }

  public function view($id = null)
{
    $jobModel = new \App\Models\JobPositionModel();

    // Only show jobs that are posted
    $job = $jobModel->where('is_posted', 1)->find($id);

    if (!$job) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Job not found');
    }

    return view('job_view', ['job' => $job]);
}

}
