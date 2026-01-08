<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Register extends BaseController
{
    public function index()
    {
        return view('register'); // your form view
    }

    public function save()
    {
        $userModel = new UserModel();

        // Generate TEMP password
        $plainPassword = bin2hex(random_bytes(4)); // 8 chars
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        $data = [
            'first_name'  => $this->request->getPost('first_name'),
            'middle_name' => $this->request->getPost('middle_name'),
            'last_name'   => $this->request->getPost('last_name'),
            'extension'   => $this->request->getPost('extension'),
            'email'       => $this->request->getPost('email'),
            'password'    => $hashedPassword,
        ];

        $userModel->insert($data);

        // SEND EMAIL
        $email = \Config\Services::email();

        $email->setTo($data['email']);
        $email->setSubject('CLSU HRMO Account Created');
        $email->setMessage("
            <h3>Welcome to CLSU HRMO</h3>
            <p>Your account has been successfully created.</p>
            <p><strong>Temporary Password:</strong> {$plainPassword}</p>
            <p>Please log in and change your password immediately.</p>
        ");

        if (!$email->send()) {
            return $email->printDebugger();
        }

        return redirect()->to('/login')->with('success', 'Account created. Check your email.');
    }
}
