<?php

namespace App\Controllers;
use App\Models\JobPositionModel;

class Jobs extends BaseController
{
    public function index()
    {
        $jobModel = new JobPositionModel();
        $data['jobs'] = $jobModel->findAll();
        return view('home', $data);
    }

    public function view($id = null)
    {
        $jobModel = new JobPositionModel();
        $data['job'] = $jobModel->find($id);

        if (!$data['job']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Job not found');
        }

        return view('job_view', $data);
    }
}
