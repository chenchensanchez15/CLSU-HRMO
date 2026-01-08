<?php

namespace App\Controllers;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        // Show login form
        return view('auth/login');
    }

    public function loginPost()
    {
        $session = session();
        $model = new UserModel();

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $model->where('email', $email)->first();

        if ($user) {
            // Check password
            if (password_verify($password, $user['password'])) {
                $session->set([
                    'user_id' => $user['id'],
                    'first_name' => $user['first_name'],
                    'email' => $user['email'],
                    'logged_in' => true
                ]);
                return redirect()->to('/dashboard'); // Redirect to dashboard view
            } else {
                $session->setFlashdata('error', 'Wrong email or password.');
                return redirect()->back();
            }
        } else {
            $session->setFlashdata('error', 'Wrong email or password.');
            return redirect()->back();
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
} 
