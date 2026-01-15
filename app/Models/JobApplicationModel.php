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
        'suffix',          
        'date_of_birth',    
        'place_of_birth',
        'sex',
        'civil_status',
        'citizenship',
        'email',           
        'phone',           
        'height',           
        'weight',          
        'blood_type',       
        'application_status',
        'applied_at',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
