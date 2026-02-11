<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantCivilServiceModel extends Model
{
    protected $table = 'applicant_civil_service';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'eligibility',
        'rating',
        'date_of_exam',
        'place_of_exam',
        'license_no',
        'license_valid_until',
        'certificate' 
    ];

    protected $useTimestamps = true;
}
