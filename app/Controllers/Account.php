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
    $photo->move(FCPATH . 'uploads', $photoName); // <-- FCPATH points to public/
    $profileData['photo'] = $photoName;
}

// Handle resume upload
$resume = $this->request->getFile('resume');
if ($resume && $resume->isValid() && !$resume->hasMoved()) {
    $resumeName = $resume->getRandomName();
    $resume->move(FCPATH . 'uploads', $resumeName); // <-- move to public/uploads
    $profileData['resume'] = $resumeName;
}


        // Check if profile exists
        $profile = $applicantModel->where('user_id', $userId)->first();

        if ($profile) {
            $applicantModel->update($profile['id'], $profileData);
        } else {
            $profileData['user_id'] = $userId;
            $applicantModel->insert($profileData);
        }

        return redirect()->to('account/personal')->with('success', 'Profile updated successfully.');
    }
}
