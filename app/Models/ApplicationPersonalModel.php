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
        'photo'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
