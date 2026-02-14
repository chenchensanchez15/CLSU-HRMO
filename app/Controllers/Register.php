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

        // Send welcome email
        $emailSent = $this->sendWelcomeEmail($data['email'], $data['first_name'], $plainPassword);

        if (!$emailSent) {
            return redirect()->back()
                ->with('swal_error', 'Account created but email failed to send.');
        }

        return redirect()->to('/login')
            ->with('registration_success', 'Account created successfully! Check your email for the temporary password.');
    }
    
    /**
     * Send welcome email to new registrants
     */
    private function sendWelcomeEmail($email, $firstName, $tempPassword)
    {
        $emailService = \Config\Services::email();
        
        // Email content
        $subject = 'Welcome to CLSU HRMO - Account Created';
        $message = $this->getWelcomeEmailTemplate($firstName, $tempPassword);
        
        // Configure email
        $emailService->setTo($email);
        $emailService->setFrom('rogelioalmerol1@gmail.com', 'CLSU HRMO');
        $emailService->setSubject($subject);
        $emailService->setMessage($message);
        
        // Send email
        return $emailService->send();
    }
    
    /**
     * Get welcome email template
     */
    private function getWelcomeEmailTemplate($firstName, $tempPassword)
    {
        // Convert to Philippine time
        $utcDateTime = new \DateTime('now', new \DateTimeZone('UTC'));
        $philippineTimeZone = new \DateTimeZone('Asia/Manila');
        $utcDateTime->setTimezone($philippineTimeZone);
        $registrationTime = $utcDateTime->format('F j, Y g:i A');
        
        $template = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #0B6B3A; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
        .password-box { background-color: #e8f5e8; border: 2px dashed #0B6B3A; padding: 15px; text-align: center; margin: 20px 0; border-radius: 5px; }
        .login-button { display: inline-block; padding: 15px 30px; background-color: #0B6B3A; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; font-size: 16px; border: 2px solid #0B6B3A; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .important { background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CLSU Human Resource Management Office</h1>
            <p>Online Job Application System</p>
        </div>
        
        <div class="content">
            <h2>Welcome ' . $firstName . '!</h2>
            
            <p>Your account has been successfully created on <strong>' . $registrationTime . '</strong>.</p>
            
            <div class="important">
                <strong>⚠ Important:</strong> This is a temporary password. You must change it on your first login.
            </div>
            
            <div class="password-box">
                <strong>Your Temporary Password:</strong><br>
                <span style="font-size: 24px; font-family: monospace; letter-spacing: 2px;">' . $tempPassword . '</span>
            </div>
            
            <p style="text-align: center;">
                <a href="http://localhost:8080/HRMO/login" class="login-button">LOGIN TO YOUR ACCOUNT</a>
            </p>
            
            <p>After logging in, you\'ll be prompted to create a new secure password.</p>
            
            <p>If you have any questions, please don\'t hesitate to contact our support team.</p>
            
            <p>Best regards,<br>
            <strong>CLSU HRMO Team</strong></p>
        </div>
        
        <div class="footer">
            &copy; 2026 CLSU-HRMO. All rights reserved.<br>
            Powered by Management Information System Office (CLSU-MISO)
        </div>
    </div>
</body>
</html>';
        
        return $template;
    }
}
