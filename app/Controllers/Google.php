<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Google extends BaseController
{
    public function redirectToGoogle()
    {
        // Redirect to the main GoogleAuth controller
        return redirect()->to('/googleAuth/redirectToGoogle');
    }
}
