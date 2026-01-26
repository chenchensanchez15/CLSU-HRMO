<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantModel extends Model
{
    protected $table = 'applicant_personal';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'date_of_birth',
        'sex',
        'civil_status',
        'citizenship',
        'phone',
        'email',
        'residential_address',
        'permanent_address',
        'photo'
    ];

    protected $useTimestamps = true; // optional, if you want CI4 to handle created_at/updated_at
}
