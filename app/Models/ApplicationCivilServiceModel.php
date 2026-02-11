<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationCivilServiceModel extends Model
{
    protected $table = 'application_civil_service';
    protected $primaryKey = 'id_application_civil_service';

    protected $allowedFields = [
        'job_application_id',
        'eligibility',
        'rating',
        'date_of_exam',
        'place_of_exam',
        'license_no',
        'license_valid_until',
        'certificate'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}