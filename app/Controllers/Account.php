<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ApplicantModel;
use App\Models\JobApplicationModel;

class Account extends BaseController
{
    public function personal()
    {
        $session = session();
        $userId = $session->get('user_id');

        $userModel = new UserModel();
        $applicantModel = new ApplicantModel();

        $user = $userModel->find($userId);
        $profile = $applicantModel->where('user_id', $userId)->first();

        return view('account/personal', [
            'user' => $user,
            'profile' => $profile
        ]);
    }
    public function update()
{
    $session = session();
    $userId = $session->get('user_id');

    $userModel = new \App\Models\UserModel();
    $applicantModel = new \App\Models\ApplicantModel();
    $jobApplicationModel = new \App\Models\JobApplicationModel();

    // --- Collect POST data for user ---
    $userData = [
        'first_name'  => $this->request->getPost('first_name') ?? '',
        'middle_name' => $this->request->getPost('middle_name') ?? '',
        'last_name'   => $this->request->getPost('last_name') ?? '',
        'extension'   => $this->request->getPost('suffix') ?? '',
        'email'       => $this->request->getPost('email') ?? ''
    ];
    $userModel->update($userId, $userData);

    // --- Handle file uploads ---
    $resumeFile = $this->request->getFile('resume');
    $photoFile  = $this->request->getFile('photo');

    $resumeName = null;
    $photoName  = null;

    if ($resumeFile && $resumeFile->isValid()) {
        $resumeName = $resumeFile->getRandomName();
        $resumeFile->move(FCPATH.'uploads', $resumeName);
    }

    if ($photoFile && $photoFile->isValid()) {
        $photoName = $photoFile->getRandomName();
        $photoFile->move(FCPATH.'uploads', $photoName);
    }

    // --- Update applicant_profiles table ---
    $profileData = [
        'first_name'          => $this->request->getPost('first_name') ?? '',
        'middle_name'         => $this->request->getPost('middle_name') ?? '',
        'last_name'           => $this->request->getPost('last_name') ?? '',
        'suffix'              => $this->request->getPost('suffix') ?? '',
        'date_of_birth'       => $this->request->getPost('date_of_birth') ?? '',
        'place_of_birth'      => $this->request->getPost('place_of_birth') ?? '',
        'sex'                 => $this->request->getPost('sex') ?? '',
        'civil_status'        => $this->request->getPost('civil_status') ?? '',
        'citizenship'         => $this->request->getPost('citizenship') ?? '',
        'height'              => $this->request->getPost('height') ?? '',
        'weight'              => $this->request->getPost('weight') ?? '',
        'blood_type'          => $this->request->getPost('blood_type') ?? '',
        'phone'               => $this->request->getPost('phone') ?? '',
        'email'               => $this->request->getPost('email') ?? '',
        'residential_address' => $this->request->getPost('residential_address') ?? '',
        'permanent_address'   => $this->request->getPost('permanent_address') ?? '',
    ];

    if ($resumeName) $profileData['resume'] = $resumeName;
    if ($photoName)  $profileData['photo']  = $photoName;

    $profile = $applicantModel->where('user_id', $userId)->first();
    if ($profile) {
        $applicantModel->update($profile['id'], $profileData);
    } else {
        $profileData['user_id'] = $userId;
        $profile = $applicantModel->insert($profileData);
        $profile['id'] = $applicantModel->getInsertID();
    }

    // --- Update job_applications table with synced personal info ---
    $jobApplicationData = [
        'first_name'  => $profileData['first_name'],
        'middle_name' => $profileData['middle_name'],
        'last_name'   => $profileData['last_name'],
        'suffix'      => $profileData['suffix'],
        'date_of_birth' => $profileData['date_of_birth'],
        'place_of_birth' => $profileData['place_of_birth'],
        'sex'         => $profileData['sex'],
        'civil_status' => $profileData['civil_status'],
        'citizenship' => $profileData['citizenship'],
        'email'       => $profileData['email'],
        'phone'       => $profileData['phone'],
        'height'      => $profileData['height'],
        'weight'      => $profileData['weight'],
        'blood_type'  => $profileData['blood_type'],
    ];

    // Update all job applications of this user
    $jobApplicationModel
        ->where('user_id', $userId)
        ->set($jobApplicationData)
        ->update();

   return $this->response->setJSON([
    'success' => true,
    'message' => 'Profile and job applications updated successfully!',
    'photo'   => $photoName ?? ($profile['photo'] ?? null)
]);
}
    public function changePassword()
    {
        $session = session();
        $userId = $session->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        return view('account/change_password', [
            'user' => $user
        ]);
    }

    public function updatePassword()
    {
        $session = session();
        $userId = $session->get('user_id');

        $current = $this->request->getPost('current_password');
        $new = $this->request->getPost('new_password');
        $confirm = $this->request->getPost('confirm_password');

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!password_verify($current, $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        if ($new !== $confirm) {
            return redirect()->back()->with('error', 'New password and confirm password do not match.');
        }

        $userModel->update($userId, [
            'password' => password_hash($new, PASSWORD_DEFAULT),
            'first_login' => 0
        ]);

        $session->set('first_login', 0);

        return redirect()->to('/dashboard')->with('success', 'Password updated successfully!');
    }
}
