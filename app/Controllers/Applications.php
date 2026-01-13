<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JobApplicationModel;
use App\Models\JobPositionModel;

class Applications extends BaseController
{
    protected $jobPositions;
    protected $jobApplications;

    public function __construct()
    {
        $this->jobPositions = new JobPositionModel();  // To get job info
        $this->jobApplications = new JobApplicationModel(); // To save applications
    }

    // Show the application form using item_no
    public function apply($item_no = null)
    {
        if (!$item_no) {
            return redirect()->to('/jobs');
        }

        $job = $this->jobPositions->where('item_no', $item_no)->first();

        if (!$job) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Job not found');
        }

        return view('apply', ['job' => $job]);
    }
 public function submit($item_no = null)
{
    $job = $this->jobPositions->where('item_no', $item_no)->first();
    if (!$job) {
        return $this->response->setStatusCode(404)->setBody('Job not found');
    }

    $postData = $this->request->getPost();
    $postData['job_position_id'] = $job['id'];
    $postData['user_id'] = session()->get('user_id') ?? null;
    $postData['application_status'] = 'Pending';
    $postData['applied_at'] = date('Y-m-d H:i:s');

    $files = ['resume', 'id_front', 'id_back', 'additional_id'];
    foreach ($files as $fileInput) {
        $file = $this->request->getFile($fileInput);
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads', $newName);
            $postData[$fileInput] = $newName;
        }
    }

    $this->jobApplications->insert($postData);

    // For AJAX: just return a success response
    return $this->response->setBody('success');
}
   
}
