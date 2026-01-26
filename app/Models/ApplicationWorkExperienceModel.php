<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationWorkExperienceModel extends Model
{
    protected $table = 'application_work_experience';
    protected $primaryKey = 'id_application_work';

    protected $allowedFields = [
        'job_application_id',
        'position_title',
        'office',
        'date_from',
        'date_to',
        'status_of_appointment',
        'govt_service'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
