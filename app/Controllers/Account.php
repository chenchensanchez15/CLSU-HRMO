<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ApplicantModel;

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

    $userModel = new UserModel();
    $applicantModel = new ApplicantModel();
    $jobApplicationModel = new \App\Models\JobApplicationModel(); // <-- Add this

    // --- Update users table ---
    $userData = [
        'first_name' => $this->request->getPost('first_name'),
        'middle_name' => $this->request->getPost('middle_name'),
        'last_name' => $this->request->getPost('last_name'),
        'extension' => $this->request->getPost('suffix'),
        'email' => $this->request->getPost('email'),
    ];
    $userModel->update($userId, $userData);

    // --- Update applicant_profiles table ---
    $profileData = [
        'first_name' => $this->request->getPost('first_name'),
        'middle_name' => $this->request->getPost('middle_name'),
        'last_name' => $this->request->getPost('last_name'),
        'suffix' => $this->request->getPost('suffix'),
        'date_of_birth' => $this->request->getPost('date_of_birth'),
        'place_of_birth' => $this->request->getPost('place_of_birth'),
        'sex' => $this->request->getPost('sex'),
        'civil_status' => $this->request->getPost('civil_status'),
        'citizenship' => $this->request->getPost('citizenship'),
        'height' => $this->request->getPost('height'),
        'weight' => $this->request->getPost('weight'),
        'blood_type' => $this->request->getPost('blood_type'),
        'phone' => $this->request->getPost('phone'),
        'email' => $this->request->getPost('email'),
        'residential_address' => $this->request->getPost('residential_address'),
        'permanent_address' => $this->request->getPost('permanent_address'),
        'education' => $this->request->getPost('education'),
        'training' => $this->request->getPost('training'),
        'experience' => $this->request->getPost('experience'),
        'eligibility' => $this->request->getPost('eligibility'),
        'competency' => $this->request->getPost('competency')
    ];

    // Handle photo upload
    $photo = $this->request->getFile('photo');
    if ($photo && $photo->isValid() && !$photo->hasMoved()) {
        $photoName = $photo->getRandomName();
        $photo->move(FCPATH . 'uploads', $photoName);
        $profileData['photo'] = $photoName;
    }

    // Check if profile exists
    $profile = $applicantModel->where('user_id', $userId)->first();

    if ($profile) {
        $applicantModel->update($profile['id'], $profileData);
    } else {
        $profileData['user_id'] = $userId;
        $applicantModel->insert($profileData);
    }

    // --- NEW: Update all job applications for this user ---
    $jobApplicationData = [
        'first_name' => $profileData['first_name'],
        'middle_name' => $profileData['middle_name'],
        'last_name' => $profileData['last_name'],
        'suffix' => $profileData['suffix'],
        'date_of_birth' => $profileData['date_of_birth'],
        'place_of_birth' => $profileData['place_of_birth'],
        'sex' => $profileData['sex'],
        'civil_status' => $profileData['civil_status'],
        'citizenship' => $profileData['citizenship']
    ];
    $jobApplicationModel->where('user_id', $userId)->set($jobApplicationData)->update();

    return redirect()->to('account/personal')->with('success', 'Profile updated successfully and synced with your job applications.');
}

    // --- Show change password form ---
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

    $userModel = new \App\Models\UserModel();
    $user = $userModel->find($userId);

    // Check if current password is correct
    if (!password_verify($current, $user['password'])) {
        return redirect()->back()->with('error', 'Current password is incorrect.');
    }

    // Check if new password matches confirm password
    if ($new !== $confirm) {
        return redirect()->back()->with('error', 'New password and confirm password do not match.');
    }

    // Update password AND set first_login = 0
    $userModel->update($userId, [
        'password' => password_hash($new, PASSWORD_DEFAULT),
        'first_login' => 0
    ]);

    // Update session so the SweetAlert won't show again
    $session->set('first_login', 0);

    return redirect()->to('/dashboard')->with('success', 'Password updated successfully!');
}

}
