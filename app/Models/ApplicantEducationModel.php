<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantEducationModel extends Model
{
    protected $table = 'applicant_education';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'degree_level_id',   // <-- added
        'degree_id',         // <-- added
        'school_name',
        'degree_course',
        'course', 
        'period_from',
        'period_to',
        'highest_level_units',
        'year_graduated',
        'awards'
    ];
}
