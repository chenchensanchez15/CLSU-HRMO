<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Register extends BaseController
{
    public function index()
    {
        return view('register');
    }

    public function save()
    {
        $rules = [
            'first_name'  => 'required',
            'last_name'   => 'required',
            'email'       => 'required|valid_email|is_unique[users.email]',
        ];

        $messages = [
            'email' => [
                'is_unique' => 'This email is already registered.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('swal_error', $this->validator->getError('email'));
        }

        $userModel = new UserModel();

        // ✅ Generate TEMP password
        $plainPassword  = bin2hex(random_bytes(4)); // 8 chars
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        $data = [
            'first_name'  => $this->request->getPost('first_name'),
            'middle_name' => $this->request->getPost('middle_name'),
            'last_name'   => $this->request->getPost('last_name'),
            'extension'   => $this->request->getPost('extension'),
            'email'       => $this->request->getPost('email'),
            'password'    => $hashedPassword,
            'role'        => 'applicant',
            'first_login' => 1
        ];

        $userModel->insert($data);

        $email = \Config\Services::email();

  $email->setFrom('hrmo@noreply.com', 'CLSU HRMO'); // Sender
$email->setTo($data['email']); // Recipient
$email->setSubject('CLSU HRMO Account Created');
$email->setMessage("
    <h3>Welcome to CLSU Online Job Application System</h3>
    <p>Your account has been successfully created.</p>
    <p><strong>Temporary Password:</strong> {$plainPassword}</p>
    <p>Please log in and change your password immediately.</p>
    <p>
        <a href='http://localhost:8080/HRMO/login' style='
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #0B6B3A;
            text-decoration: none;
            border-radius: 5px;
        '>Log In Here</a>
    </p>
");

        if (!$email->send()) {
            return redirect()->back()
                ->with('swal_error', 'Account created but email failed to send.');
        }

        return redirect()->to('/login')
            ->with('swal_success', 'Account created successfully. Check your email.');
    }
}
