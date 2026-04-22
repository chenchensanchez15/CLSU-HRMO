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
                'first_login' => $user['first_login'],
                'created_by' => $user['created_by'] ?? 0
            ]);

            // If first login, always redirect to dashboard to change password
            if ($user['first_login'] == 1) {
                // Store redirect URL if exists (for after password change)
                $redirectUrl = $session->get('redirect_after_login');
                if ($redirectUrl) {
                    $session->set('redirect_after_password_change', $redirectUrl);
                }
                // Set flag that profile needs to be completed after password change
                $session->set('profile_completion_required', true);
                return redirect()->to('/dashboard')->with('first_login', true);
            }

            // Check if there's a redirect URL stored (for job application)
            $redirectUrl = $session->get('redirect_after_login');
            
            if ($redirectUrl) {
                // Clear the redirect URL from session
                $session->remove('redirect_after_login');
                return redirect()->to($redirectUrl);
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
