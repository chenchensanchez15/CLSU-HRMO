<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantWorkExperienceModel extends Model
{
    protected $table = 'applicant_work_experience';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'application_id',
        'current_work',
        'previous_work',
        'duration',
        'awards',
        'created_at',
        'updated_at'
    ];
}
