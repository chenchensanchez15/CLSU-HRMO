<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationTrainingsModel extends Model
{
    protected $table      = 'application_trainings';
    protected $primaryKey = 'id_application_trainings';
    protected $allowedFields = [
        'job_application_id',
        'training_category_id',
        'training_venue',
        'training_name',
        'date_from',
        'date_to',
        'training_facilitator',
        'training_hours',
        'training_sponsor',
        'training_remarks',
        'certificate_file',
        'added_date'
    ];

    protected $useTimestamps = true; // will automatically handle created_at and updated_at if your table has them
}
