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

    /**
     * Get all posted jobs for modal use
     */
    public function getAllPosted()
    {
        // Set JSON response header
        header('Content-Type: application/json');
        
        try {
            $jobModel = new \App\Models\JobPositionModel();
            
            // Get all posted jobs
            $jobs = $jobModel->where('is_posted', 1)->findAll();
            
            return $this->response->setJSON([
                'success' => true,
                'jobs' => $jobs
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while fetching jobs'
            ]);
        }
    }

    /**
     * Get job details via AJAX for modal
     */
    public function getDetails($id = null)
    {
        // Set JSON response header
        header('Content-Type: application/json');
        
        try {
            $jobModel = new \App\Models\JobPositionModel();
            
            // Only show jobs that are posted
            $job = $jobModel->where('is_posted', 1)->find($id);
            
            if (!$job) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Job not found'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'job' => $job
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while fetching job details'
            ]);
        }
    }

}
