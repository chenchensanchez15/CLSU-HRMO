<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantEducationModel extends Model
{
    protected $table = 'applicant_education';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'application_id',
        'level',
        'school_name',
        'location',
        'year_graduated',
        'awards'
    ];
}

