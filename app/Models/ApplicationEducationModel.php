<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationEducationModel extends Model
{
    protected $table = 'application_education';
    protected $primaryKey = 'id_application_education';

    protected $allowedFields = [
        'job_application_id',
        'degree_level_id',
        'degree_id',
        'school_name',
        'degree_course',
        'course',
        'period_from',
        'period_to',
        'highest_level_units',
        'year_graduated',
        'awards'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
