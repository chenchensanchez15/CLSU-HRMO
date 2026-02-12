<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationPersonalModel extends Model
{
    protected $table = 'application_personal';
    protected $primaryKey = 'id_application_personal';

    protected $allowedFields = [
        'job_application_id',
        'first_name',
        'last_name',
        'middle_name',
        'extension',
        'sex',
        'date_of_birth',
        'civil_status',
        'email',
        'phone',
        'citizenship',
        'residential_address',
        'permanent_address',
        'photo',
        'is_clsu_employee',
        'clsu_employee_type',
        'clsu_employee_specify',
        'religion',
        'is_indigenous',
        'indigenous_specify',
        'is_pwd',
        'pwd_type',
        'pwd_specify',
        'is_solo_parent'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
