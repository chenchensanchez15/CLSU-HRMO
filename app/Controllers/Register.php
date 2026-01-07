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
        $validation =  \Config\Services::validation();

        // Validate inputs
        $validation->setRules([
            'first_name' => 'required|min_length[2]',
            'middle_name'=> 'permit_empty',
            'last_name'  => 'required|min_length[2]',
            'extension'  => 'permit_empty',
            'email'      => 'required|valid_email|is_unique[users.email]'
        ]);

        if (!$this->validate($validation->getRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare data
        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'middle_name'=> $this->request->getPost('middle_name'),
            'last_name'  => $this->request->getPost('last_name'),
            'extension'  => $this->request->getPost('extension'),
            'email'      => $this->request->getPost('email'),
            // Generate random password
            'password'   => password_hash(bin2hex(random_bytes(4)), PASSWORD_DEFAULT),
        ];

        $model = new UserModel();
        $model->save($data);

        return redirect()->to(base_url('login'))->with('success', 'Registration successful! Please check your email for login credentials.');
    }
}
