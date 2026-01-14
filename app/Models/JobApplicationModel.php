<?php

namespace App\Models;

use CodeIgniter\Model;

class JobApplicationModel extends Model
{
    protected $table = 'job_applications';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'job_position_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',           // was name_extension in form
        'date_of_birth',    // was birth_date in form
        'place_of_birth',
        'sex',
        'civil_status',
        'citizenship',
        'application_status',
        'applied_at',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
