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
        if (password_verify($password, $user['password'])) {

            // Set basic session
            $session->set([
                'user_id' => $user['id'],
                'first_name' => $user['first_name'],
                'email' => $user['email'],
                'logged_in' => true,
                'first_login' => $user['first_login']
            ]);

            // If first login, redirect to dashboard with flashdata
            if ($user['first_login'] == 1) {
                return redirect()->to('/dashboard')->with('first_login', true);
            }

            // Normal login
            return redirect()->to('/dashboard');

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
